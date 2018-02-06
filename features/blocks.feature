Feature:
  In order to separate the variables into sensible blocks with explainations
  As a user
  I want to define my blocks in the .env.dist file

  Scenario: Displays the block title
    Given the file ".env.dist" contains:
    """
    ## Something
    MY_VARIABLE=default-value
    """
    When I run the companion with the following answers:
      | Let's fix this? (y)           | y        |
      | MY_VARIABLE ? (default-value) | my-value |
    Then the companion's output will look like that:
    """
    It looks like you are missing some configuration (1 variables). I will help you to sort this out.
    Let's fix this? (y)

    Something

    MY_VARIABLE ? (default-value)
    """

  Scenario: Displays the block description
    Given the file ".env.dist" contains:
    """
    ## Something
    # With more details, so it's clearer to the user...
    MY_VARIABLE=default-value
    """
    When I run the companion with the following answers:
      | Let's fix this? (y)           | y        |
      | MY_VARIABLE ? (default-value) | my-value |
    Then the companion's output will look like that:
    """
    It looks like you are missing some configuration (1 variables). I will help you to sort this out.
    Let's fix this? (y)

    Something
    With more details, so it's clearer to the user...

    MY_VARIABLE ? (default-value)
    """
