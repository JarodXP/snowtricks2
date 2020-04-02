#Creates the swiftmailer.yaml config file
echo "swiftmailer:
      url: '%env(MAILER_URL)%'
      spool: { type: 'memory' }">config/packages/swiftmailer.yaml