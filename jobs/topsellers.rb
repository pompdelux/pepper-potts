require 'net/http'
require 'json'

config = YAML.load File.open("config.yml")
config = config[:hanzo]

SCHEDULER.every '10m', :first_in => 0 do |job|
  response = JSON.parse(Net::HTTP.get(URI(config[:dsn] + "?action=topsellers")))

  if !response
    puts "communication error with: " + config[:dsn] + "?action=topsellers"
  else
    data = [];

    response.each do |item|
      data.push({
        label: item['products_name'].slice(0, 20),
        value: item['highscore']
      });
    end

    send_event('topsellers', { items: data })
  end
end
