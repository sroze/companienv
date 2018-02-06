Feature:
  In order to fill my .env file
  As a developer
  I want the companion to ask me the values of each missing variable, from a .env.dist file

  Scenario: It asks all the variables if the file is missing
    Given the file ".env.dist" contains:
    """
    ## Something
    MY_VARIABLE=default-value
    """
    When I run the companion with the following answers:
      | Let's fix this? (y)           | y        |
      | MY_VARIABLE ? (default-value) | my-value |
    And the file ".env" should contain:
    """
    MY_VARIABLE=my-value
    """

  Scenario: It asks only the missing variables
    Given the file ".env.dist" contains:
    """
    ## Something
    MY_VARIABLE=default-value
    A_NEW_VARIABLE=
    """
    And the file ".env" contains:
    """
    MY_VARIABLE=something-else

    """
    When I run the companion with the following answers:
      | Let's fix this? (y) | y     |
      | A_NEW_VARIABLE ?    | value |
    And the file ".env" should contain:
    """
    MY_VARIABLE=something-else
    A_NEW_VARIABLE=value
    """

  Scenario: We ask an empty variable if it has a value
    Given the file ".env.dist" contains:
    """
    ## Something
    MY_VARIABLE=default-value
    """
    And the file ".env" contains:
    """
    MY_VARIABLE=
    """
    When I run the companion with the following answers:
      | Let's fix this? (y)           | y        |
      | MY_VARIABLE ? (default-value) |          |
    And the file ".env" should contain:
    """
    MY_VARIABLE=
    """

  Scenario: We do not ask an empty variable if the reference is empty
    Given the file ".env.dist" contains:
    """
    ## Something
    EMPTY_VARIABLE=
    """
    And the file ".env" contains:
    """
    EMPTY_VARIABLE=
    """
    When I run the companion with the following answers:
      | Let's fix this? (y) | y        |
    And the file ".env" should contain:
    """
    EMPTY_VARIABLE=
    """

  Scenario: We do not ask for variable than have a falsy value
    Given the file ".env.dist" contains:
    """
    ## Something
    MY_VARIABLE=default-value
    """
    And the file ".env" contains:
    """
    MY_VARIABLE=0
    """
    When I run the companion with the following answers:
      | Let's fix this? (y)           | y        |
    And the file ".env" should contain:
    """
    MY_VARIABLE=0
    """

  Scenario: It supports variables containing equals sign
    Given the file ".env.dist" contains:
    """
    ## Something
    A_BASE64_VALUE=abc123=
    """
    And the file ".env" contains:
    """
    A_BASE64_VALUE=
    """
    When I run the companion with the following answers:
      | Let's fix this? (y)        | y       |
      | A_BASE64_VALUE ? (abc123=) | abc123= |
    And the file ".env" should contain:
    """
    A_BASE64_VALUE=abc123=
    """
