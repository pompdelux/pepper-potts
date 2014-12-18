require 'net/http'
require 'json'

config = YAML.load File.open("config.yml")
config = config[:hanzo]

SCHEDULER.every '10m', :first_in => 0 do |job|
  # response = JSON.parse(Net::HTTP.get(URI(config[:dsn] + "?action=ordercount")))
  targets = [
    "target=alias(summarize(sumSeries(stats.counters.prod.order.*AT.count)%2C%221h%22)%2C%22.AT%22)",
    "target=alias(summarize(sumSeries(stats.counters.prod.order.*CH.count)%2C%221h%22)%2C%22.CH%22)",
    "target=alias(summarize(sumSeries(stats.counters.prod.order.COM.count)%2C%221h%22)%2C%22COM%22)",
    "target=alias(summarize(sumSeries(stats.counters.prod.order.*DE.count)%2C%221h%22)%2C%22.DE%22)",
    "target=alias(summarize(sumSeries(stats.counters.prod.order.*DK.count)%2C%221h%22)%2C%22.DK%22)",
    "target=alias(summarize(sumSeries(stats.counters.prod.order.*FI.count)%2C%221h%22)%2C%22.FI%22)",
    "target=alias(summarize(sumSeries(stats.counters.prod.order.*NL.count)%2C%221h%22)%2C%22.NL%22)",
    "target=alias(summarize(sumSeries(stats.counters.prod.order.*NO.count)%2C%221h%22)%2C%22.NO%22)",
    "target=alias(summarize(sumSeries(stats.counters.prod.order.*SE.count)%2C%221h%22)%2C%22.SE%22)",
    "target=alias(summarize(sumSeries(stats.counters.prod.order.*.count)%2C%221h%22)%2C%22ALL%22)"
  ].join('&')

  response = JSON.parse(Net::HTTP.get(URI("http://pdlstats1.pompdelux.com/render/?_salt=1418900351.465&from=-1hours&"+targets+"&format=json")))

  if !response
    puts "communication error with: " + config[:dsn] + "?action=ordercount"
  else
    # send_event('ordercount', { items: response })

    data = [];

    response.each do |item|
      data.push({
        label: item['target'],
        value: "#{sprintf('%.0f', item['datapoints'][0][0])} ~ #{sprintf('%.0f', item['datapoints'][1][0])}"
      });
    end

    send_event('ordercount2', { items: data })
  end
end
