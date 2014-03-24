require 'net/http'
require 'json'
require 'pp'

config = YAML.load File.open("config.yml")
config = config[:hanzo]

SCHEDULER.every '10m', :first_in => 0 do |job|
  response = JSON.parse(Net::HTTP.get(URI(config[:dsn] + "?action=topsellers&filter=G")))

  if !response
    puts "communication error with: " + config[:dsn] + "?action=topsellers&filter=G"
  else
    data = [];

    response.each do |item|
      data.push({
        label: item['products_name'].slice(0, 20),
        value: item['highscore']
      });
    end

    send_event('topsellers-girl', { items: data })
  end

  response = JSON.parse(Net::HTTP.get(URI(config[:dsn] + "?action=topsellers&filter=B")))

  if !response
    puts "communication error with: " + config[:dsn] + "?action=topsellers&filter=B"
  else
    data = [];

    response.each do |item|
      data.push({
        label: item['products_name'].slice(0, 20),
        value: item['highscore']
      });
    end

    send_event('topsellers-boy', { items: data })
  end

  response = JSON.parse(Net::HTTP.get(URI(config[:dsn] + "?action=topsellers&filter=LB")))

  if !response
    puts "communication error with: " + config[:dsn] + "?action=topsellers&filter=LB"
  else
    data = [];

    response.each do |item|
      data.push({
        label: item['products_name'].slice(0, 20),
        value: item['highscore']
      });
    end

    send_event('topsellers-little-boy', { items: data })
  end

  response = JSON.parse(Net::HTTP.get(URI(config[:dsn] + "?action=topsellers&filter=LG")))

  if !response
    puts "communication error with: " + config[:dsn] + "?action=topsellers&filter=LG"
  else
    data = [];

    response.each do |item|
      data.push({
        label: item['products_name'].slice(0, 20),
        value: item['highscore']
      });
    end

    send_event('topsellers-little-girl', { items: data })
  end
end
