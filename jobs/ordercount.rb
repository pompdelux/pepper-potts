require 'net/http'
require 'json'

config = YAML.load File.open("config.yml")
config = config[:hanzo]

SCHEDULER.every '10m', :first_in => 0 do |job|
  response = JSON.parse(Net::HTTP.get(URI(config[:dsn] + "?action=ordercount")))

  if !response
    puts "communication error with: " + config[:dsn] + "?action=ordercount"
  else
    send_event('ordercount', { items: response })
  end
end
