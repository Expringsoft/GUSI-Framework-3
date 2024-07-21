<p align="center">
  <a href="https://gusi-framework.expringsoft.com/v3/">
    <img src="https://gusi-framework.expringsoft.com/Resources/Images/App_Logo.svg" alt="GUSI Framework" width="200" height="200">
  </a>
</p>

<h3 align="center">GUSI Framework v3</h3>

<p align="center">
  Power and simplicity for modern PHP applications.
  <br>
  <a href="https://gusi-framework.expringsoft.com/docs/introduction"><strong>View docs</strong></a>
  <br>
  <br>
  <a href="https://github.com/Expringsoft/GUSI-Framework-3/issues/new?assignees=-&labels=bug&template=bug_report.yml">Report bug</a>
  ·
  <a href="https://github.com/Expringsoft/GUSI-Framework-3/issues/new?assignees=&labels=feature&template=feature_request.yml">Request feature</a>
</p>

# GUSI Framework v3
![MIT License](https://img.shields.io/badge/license-MIT-blue.svg)
![Downloads](https://img.shields.io/github/downloads/Expringsoft/GUSI-Framework-3/total.svg)

GUSI Framework is a PHP framework designed for modern applications, compatible with PHP >= 8.1. This framework provides a robust and modular structure for web application development, focusing on simplicity and efficiency. The goal is to provide a solid foundation for web applications that can easily scale and ensure their portability and use in shared or dedicated hosting environments.

## Features

- **Modular Structure**: Organize your application into modules for better maintainability.
- **Integrated ORM**: Provides an abstract `Model` class for easy and efficient database interaction. The `Model` class offers basic CRUD (Create, Read, Update, Delete) operations.
- **File Management**: `FileManager` class to handle file and directory operations and uploads.
- **Internationalization**: Support for multiple languages with JSON translation files.
- **Centralized Configuration**: `Configuration` class to manage application settings.
- **Cache System**: Integrated cache configuration and management.
- **Controllers and Views**: Clear separation between business logic and presentation.
- **Encryption**: provide a basic and secure implementation for encrypting data.

## Installation

### On your machine

1. Clone the repository:
    ```sh
    git clone https://github.com/Expringsoft/GUSI-Framework-3.git your-project-folder
    ```

2. Navigate to the project directory:
    ```sh
    cd your-project-folder
    ```
### Set up your server.

1. Make sure you have PHP 8.1 or higher installed:
    ```sh
    php -v
    ```

2. Enable mod_rewrite module if it is not active:
    ```sh
    sudo a2enmod rewrite
    ```

3. Restart your server to apply the changes:
    ```sh
	sudo systemctl restart apache2
    ```

### Optional server configuration:

1. To enable file compression, activate mod_deflate:
    ```sh
    sudo a2enmod deflate
    ```

2. To enable file caching and basic header-security options, activate mod_headers:
    ```sh
    sudo a2enmod headers
    ```

3. For increased security, server protection, and to limit early file uploads, it is recommended to install security2_module and activate it with the OWASP CRS rules. First, install ModSecurity:
    ```sh
    sudo apt update
	sudo apt install libapache2-mod-security2
    ```

4. Enable mod_rewrite module if it is not active:
    ```sh
    sudo a2enmod security2
    ```

5. Download and set up OWASP CRS:
	```sh
    cd /etc/modsecurity
	sudo git clone https://github.com/coreruleset/coreruleset.git
	sudo mv coreruleset/crs-setup.conf.example crs-setup.conf
	sudo mv coreruleset/rules/ .
    ```

5. Restart your server to apply the changes:
    ```sh
	sudo systemctl restart apache2
    ```


## Usage and Configuration

### First steps

The application configuration is managed through the [`Configuration`](App/Core/Application/Configuration.php) class. Here you can define parameters such as maximum storage usage, maximum upload size, cache settings, and database configuration.

### Set your development enviroment
You can specify to your web application if it is running in a [development environment](App/Core/Application/Configuration.php#L47), which will modify the behavior of various functions and routing, as well as prevent the printing of errors when running on production.

### Adjust your web application to your needs.
Whether you have your web application on shared or dedicated hosting, you can configure route generation without needing to modify your domain settings or use .htaccess files. Simply set the [path](App/Core/Application/Configuration.php#L21) where your application is located (in a local environment) or, when in production, set the domain and base path. This configuration will be considered in the printing of routes and redirection.

You can set a [storage usage limit](App/Core/Application/Configuration.php#L96) for your web application, as well as the [minimum available disk storage](App/Core/Application/Configuration.php#L101). Additionally, you can also set a [limit on the size of files uploaded](App/Core/Application/Configuration.php#L107) to your server, either globally or per upload. Choose the option that best suits your needs.

### Modules

[`Modules`](App/Core/Framework/Abstracts/Module.php) encompass a set of functions and sections of your web application. They are responsible for registering the access routes for each request to your server and serve to group and channel controllers. Each module can configure a channel, and you can use this to limit or control in which scenarios or conditions actions or accesses can be executed on your server. For example, you can set up a BETA channel for a module so that only certain users can access the routes it registers or specific sections depending on the channel. This concept will evolve over time to provide more controls and tools for various use cases.

### Controllers
Controllers are responsible for handling incoming requests to the web application, processing the necessary data, and returning appropriate responses. They act as intermediaries between models and views, coordinating the application's logic. Controllers are also able to be channeled, allowing them to set specific channels and perform operations accordingly, just like modules. Controllers are expected to render content to the user, as seen in [`Home.php`](App/Controllers/Index/Home.php), which renders the main page and also serves the favicon. This can be configured within the logic to be executed. Once the logic is executed, the view will be rendered if the execution is successful; otherwise, an error page will be displayed, depending on the case.

### Models
To create models that interact with the database, extend the abstract [`Model`](App/Core/Framework/Model.php) class and implement the [`Modelable`](App/Core/Framework/Interfaces/Modelable.php) interface. This allows you to define the structure and behavior of your models easily. The `Model` class provides basic CRUD methods.

### Views

Views are located in the `Views/` directory. Once you set a view to a controller, you can pass data through an array as a parameter. This way, you can perform operations within the views and maintain data consistency. Additionally, you can call helper classes in .php views like [`Actions`](App/Core/Server/Actions.php), which allows you to print localized texts to display according to the language of your app or the client, as well as methods to print routes, resources, etc. An example view is [`Home.php`](Views/Default/Home.php).

### Internationalization

You can serve your web application in different languages using [`LanguageManager`](App/Core/Framework/Classes/LanguageManager.php) or the `printLocalized` method of [`Actions`](App/Core/Server/Actions.php), For this, you need to provide the key of the localized text you want to display from your translation file. Translation files are located in the `App/Langs/` directory. The default language file is [`default.json`](App/Langs/default.json). If a translation file or text key is not found, a text from the default language file will be printed instead. Translation files must be .json files named after the translation language, for example [`es-419.json`](App/Langs/es-419.json).

## Additional Classes and Structures

### Classes

- **Cryptography**: Provides methods for encrypting and decrypting data, ensuring secure data handling. [App/Core/Framework/Security/Cryptography.php](App/Core/Framework/Security/Cryptography.php)
- **Regex**: Contains utility methods for working with regular expressions. [App/Core/Framework/Classes/Regex.php](App/Core/Framework/Classes/Regex.php)
- **UnitTest**: Facilitates unit testing by providing methods to define and run tests. [App/Core/Framework/Classes/UnitTest.php](App/Core/Framework/Classes/UnitTest.php)
- **QueryBuilder**: Helps in building SQL queries programmatically, making database interactions more intuitive. [App/Core/Framework/Classes/QueryBuilder.php](App/Core/Framework/Classes/QueryBuilder.php)
- **LanguageManager**: Manages language settings and translations for the application. [App/Core/Framework/Classes/LanguageManager.php](App/Core/Framework/Classes/LanguageManager.php)
- **TestObject**: Represents a test case or a set of test cases for unit testing. [App/Core/Framework/Classes/TestObject.php](App/Core/Framework/Classes/TestObject.php)

### Structures

- **APIResponse**: Standardizes the structure of API responses, ensuring consistency across the application. [App/Core/Framework/Structures/APIResponse.php](App/Core/Framework/Structures/APIResponse.php)
- **Operation**: Represents the result of an operation, including success status and messages. [App/Core/Framework/Structures/Operation.php](App/Core/Framework/Structures/Operation.php)
- **Collection**: Provides a structure for managing a collection of items, with methods for common operations. [App/Core/Framework/Structures/Collection.php](App/Core/Framework/Structures/Collection.php)
- **DatabaseResult**: Encapsulates the result of a database query, providing methods to access the data. [App/Core/Framework/Structures/DatabaseResult.php](App/Core/Framework/Structures/DatabaseResult.php)

## Contribution

1. Fork the project.
2. Create a new branch (`git checkout -b feature/new-feature`).
3. Make your changes and commit them (`git commit -am 'Add new feature'`).
4. Push your changes (`git push origin feature/new-feature`).
5. Open a Pull Request.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.