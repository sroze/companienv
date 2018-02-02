Feature:
  In order to present only relevant options to the user
  I want to ask values for a given variable only if another variable has a given value

  Scenario: It does ask all the variables if condition is false
    Given the file ".env.dist" contains:
    """
    ## Development & Audit
    #
    #+only-if(GOOGLE_CLOUD_AUDIT_LOG_PROJECT_ID):(GOOGLE_CLOUD_AUDIT_ENABLED=true)
    #+only-if(GOOGLE_CLOUD_AUDIT_LOG_SERVICE_ACCOUNT_PATH):(GOOGLE_CLOUD_AUDIT_ENABLED=true)
    GOOGLE_CLOUD_AUDIT_ENABLED=false
    GOOGLE_CLOUD_AUDIT_LOG_PROJECT_ID=
    GOOGLE_CLOUD_AUDIT_LOG_SERVICE_ACCOUNT_PATH=/runtime/keys/google-cloud-audit-log.json
    """
    When I run the companion with the following answers:
      | Let's fix this? (y)                                                                       | y                                         |
      | GOOGLE_CLOUD_AUDIT_ENABLED ? (false)                                                      | true                                      |
      | GOOGLE_CLOUD_AUDIT_LOG_PROJECT_ID ?                                                       | project-id                                |
      | GOOGLE_CLOUD_AUDIT_LOG_SERVICE_ACCOUNT_PATH ? (/runtime/keys/google-cloud-audit-log.json) | /runtime/keys/google-cloud-audit-log.json |
    And the file ".env" should contain:
    """
    GOOGLE_CLOUD_AUDIT_ENABLED=true
    GOOGLE_CLOUD_AUDIT_LOG_PROJECT_ID=project-id
    GOOGLE_CLOUD_AUDIT_LOG_SERVICE_ACCOUNT_PATH=/runtime/keys/google-cloud-audit-log.json
    """

  Scenario: It does not ask about the variable if condition is false
    Given the file ".env.dist" contains:
    """
    ## Development & Audit
    #
    #+only-if(GOOGLE_CLOUD_AUDIT_LOG_PROJECT_ID):(GOOGLE_CLOUD_AUDIT_ENABLED=true)
    #+only-if(GOOGLE_CLOUD_AUDIT_LOG_SERVICE_ACCOUNT_PATH):(GOOGLE_CLOUD_AUDIT_ENABLED=true)
    GOOGLE_CLOUD_AUDIT_ENABLED=false
    GOOGLE_CLOUD_AUDIT_LOG_PROJECT_ID=
    GOOGLE_CLOUD_AUDIT_LOG_SERVICE_ACCOUNT_PATH=/runtime/keys/google-cloud-audit-log.json
    """
    When I run the companion with the following answers:
      | Let's fix this? (y)                  | y     |
      | GOOGLE_CLOUD_AUDIT_ENABLED ? (false) | false |
    And the file ".env" should contain:
    """
    GOOGLE_CLOUD_AUDIT_ENABLED=false
    GOOGLE_CLOUD_AUDIT_LOG_PROJECT_ID=
    GOOGLE_CLOUD_AUDIT_LOG_SERVICE_ACCOUNT_PATH=/runtime/keys/google-cloud-audit-log.json
    """
