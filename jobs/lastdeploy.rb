=begin
require 'net/http'
require 'json'

class LastDeploy
  def initialize()
  end

  def getData()
    response = JSON.parse(Net::HTTP.get(URI("http://193.84.27.240/remote/hooks.php?action=lastdeploy")))

    if !response
      puts "\e[33mcommunication error with: http://193.84.27.240/remote/hooks.php?action=lastdeploy\e[0m"
    else
      data = {
        text: response['ts'],
        moreinfo: response['by']
      };

      data
    end
  end
end

@LastDeploy = LastDeploy.new();

SCHEDULER.every '2m', :first_in => 0 do |job|
  data = @LastDeploy.getData
  send_event('lastdeploy', data)
end
=end
