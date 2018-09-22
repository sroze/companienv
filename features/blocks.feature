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

  Scenario: I ignores the commented variables
    Given the file ".env.dist" contains:
    """
    ## Something
    #A_HIDDEN_VARIABLE=it-was-useful
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

  Scenario: We do not require a block
    Given the file ".env.dist" contains:
    """
    MY_VARIABLE=default-value
    """
    When I run the companion with the following answers:
      | Let's fix this? (y)           | y        |
      | MY_VARIABLE ? (default-value) | my-value |
    Then the companion's output will look like that:
    """
    It looks like you are missing some configuration (1 variables). I will help you to sort this out.
    Let's fix this? (y)

    MY_VARIABLE ? (default-value)
    """

  Scenario: It uses the package name from Symfony's blocks
    Given the file ".env.dist" contains:
    """
    # This file is a "template" of which env vars need to be defined for your application
    # Copy this file to .env file for development, create environment variables when deploying to production
    # https://symfony.com/doc/current/best_practices/configuration.html#infrastructure-related-configuration

    ###> symfony/framework-bundle ###
    APP_ENV=dev
    APP_SECRET=e84c863b4b602b0907db13261d7d4851
    #TRUSTED_PROXIES=127.0.0.1,127.0.0.2
    #TRUSTED_HOSTS=localhost,example.com
    ###< symfony/framework-bundle ###

    ###> sroze/enqueue-bridge ###
    ENQUEUE_DSN=something
    ###< sroze/enqueue-bridge ###
    """
    When I run the companion with the following answers:
      | Let's fix this? (y)           | y        |
      | APP_ENV ? (dev) | my-value |
      | APP_SECRET ? (e84c863b4b602b0907db13261d7d4851) | e84c863b4b602b0907db13261d7d4851 |
      | ENQUEUE_DSN ? (something) | something |
    Then the companion's output will look like that:
    """
    It looks like you are missing some configuration (3 variables). I will help you to sort this out.
    Let's fix this? (y)

    > symfony/framework-bundle

    APP_ENV ? (dev)
    APP_SECRET ? (e84c863b4b602b0907db13261d7d4851)

    > sroze/enqueue-bridge

    ENQUEUE_DSN ? (something)
    """
