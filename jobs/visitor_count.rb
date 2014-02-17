require 'google/api_client'
require 'date'

config = YAML.load File.open("config.yml")
config = config[:google]

client = Google::APIClient.new(
  :application_name    => config[:application_name],
  :application_version => '0.01'
)

key = Google::APIClient::KeyUtils.load_from_pkcs12(config[:key_file], config[:key_secret])
client.authorization = Signet::OAuth2::Client.new(
  :token_credential_uri => 'https://accounts.google.com/o/oauth2/token',
  :audience             => 'https://accounts.google.com/o/oauth2/token',
  :scope                => 'https://www.googleapis.com/auth/analytics.readonly',
  :issuer               => config[:service_account_email],
  :signing_key          => key
)

SCHEDULER.every '1m', :first_in => 0 do
  client.authorization.fetch_access_token!
  analytics = client.discovered_api('analytics','v3')

  data = []
  total = 0;
  config[:profiles].each do |profile_id, label|
    count = 0

    if profile_id != ''
      visitCount = client.execute(:api_method => analytics.data.realtime.get, :parameters => {
        'ids'        => "ga:" + profile_id,
        'dimensions' => 'ga:medium',
        'metrics'    => "ga:activeVisitors"
      })

      count = visitCount.data.totalsForAllResults["ga:activeVisitors"]
      total += count.to_i
    end

    data.push({
      label: label,
      value: count
    });
  end

  data.push({
    label: 'Total',
    value: total
  })

  send_event('visitor-count', { items: data })
end
