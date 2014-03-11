require 'dashing'

$config = YAML.load File.open("config.yml")
$config = $config[:dashing]

configure do
  set :auth_token, $config[:auth_token]
  set :protection, :except => :frame_options
  set :default_dashboard, $config[:default_dashboard]

  helpers do
    def protected!
      # Put any authentication code you want in here.
      # This method is run before accessing any resource.
      unless authorized?
        response['WWW-Authenticate'] = %(Basic realm="Restricted Area")
        throw(:halt, [401, "Not authorized\n"])
      end
    end

    def authorized?

      @auth ||=  Rack::Auth::Basic::Request.new(request.env)
      @auth.provided? && @auth.basic? && @auth.credentials && @auth.credentials == [
        $config[:auth_user],
        $config[:auth_pwd]
      ]
    end
  end
end

map Sinatra::Application.assets_prefix do
  run Sinatra::Application.sprockets
end

run Sinatra::Application
