require 'chronic_duration'
require 'date'
require 'json'
require 'monitis-SDK'

config = YAML.load File.open("config.yml")
config = config[:monitis]

SCHEDULER.every '1m', :first_in => 0 do

  contact = Contact.new(
    config[:api_key],
    config[:api_secret],
    true
  )

  response = contact.getRecentAlerts(0, '', '', 10)

  rows = response['data'].map do |record|
    fd = DateTime.parse(record['failDate'])
    rd = DateTime.parse(record['recDate'])

    {
      name:      record['dataName'].slice(0, 20),
      down:      fd.strftime("%d/%m/%y %R"),
      up:        rd.strftime("%d/%m/%y %R"),
      down_time: ChronicDuration.output(record['downTime'], :format => :short)
    }
  end if response

  status = response['status']

  send_event('monitis-recent-alerts', { rows: rows, status: status })
end
