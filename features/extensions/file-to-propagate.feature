Feature:
  In order to configure my application with a file such as a downloaded service account or a given key
  As a user
  I want to give the path of my downloaded file to Companienv, so it takes care about the rest

  Scenario: It gets the file and copies it to the right place
    Given the file ".env.dist" contains:
    """
    ## GitHub
    #+file-to-propagate(GITHUB_INTEGRATION_PRIVATE_KEY_PATH)
    GITHUB_INTEGRATION_PRIVATE_KEY_PATH=/runtime/keys/github.pem
    """
    And the file "/tmp/file-to-propagate" contains:
    """
    SOMETHING
    """
    When I run the companion with the following answers:
      | Let's fix this? (y)                                                            | y                      |
      | GITHUB_INTEGRATION_PRIVATE_KEY_PATH: What is the path of your downloaded file? | /tmp/file-to-propagate |
    And the file ".env" should contain:
    """
    GITHUB_INTEGRATION_PRIVATE_KEY_PATH=/runtime/keys/github.pem
    """
    And the file "/runtime/keys/github.pem" should contain:
    """
    SOMETHING
    """

  Scenario: It does not consider the variable as missing if we it not included in the "only-if"
    Given the file ".env.dist" contains:
    """
    ## Y'a know...
    # This is a configuration.
    #+file-to-propagate(GOOGLE_CLOUD_AUDIT_LOG_SERVICE_ACCOUNT_PATH)
    #+only-if(GOOGLE_CLOUD_AUDIT_LOG_SERVICE_ACCOUNT_PATH):(GOOGLE_CLOUD_AUDIT_ENABLED=true)
    GOOGLE_CLOUD_AUDIT_ENABLED=false
    GOOGLE_CLOUD_AUDIT_LOG_SERVICE_ACCOUNT_PATH=/runtime/keys/google-cloud-audit-log.json
    """
    And the file ".env" contains:
    """
    GOOGLE_CLOUD_AUDIT_ENABLED=false
    GOOGLE_CLOUD_AUDIT_LOG_SERVICE_ACCOUNT_PATH=/runtime/keys/google-cloud-audit-log.json
    """
    When I run the companion
    Then the companion's output should be empty
