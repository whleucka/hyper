# Nebula

🌠 Nebula is a powerful PHP web framework inspired by the vastness and beauty of the cosmos. 🌟

💫 It provides developers with a flexible and extensible architecture to build custom web applications with ease. 🔭

👷 *Currently under development*

**Not for production use**

### Features
- [x] Modular Design: Nebula follows a modular approach, allowing you to organize your code into reusable and independent components.
- [x] Routing: Efficient routing system for handling URL mapping and request handling.
- [x] Template Engine: Built-in template engine for easy and flexible view rendering.
- [ ] Database Abstraction: Simplified database interactions with a built-in ORM (Object-Relational Mapping) layer.
- [ ] Form Handling: Convenient form handling and validation capabilities.
- [x] Security: Integrated security measures to protect against common web vulnerabilities.
- [x] Caching: Caching mechanisms to improve performance and optimize data retrieval.
- [x] Error Handling: Comprehensive error handling and logging for efficient debugging.


### Getting Started

To get started with Nebula, follow these steps:

- **Installation**: Clone the repository and install dependencies using Composer.
```bash
git clone https://github.com/libra-php/nebula.git
cd nebula
composer install
```

- **Configuration**: Customize the configuration files according to your project requirements, including database settings and routes.
```
# Copy the example settings
cp .env.example .env
# Change ownership of the view cache
chown -R www-data:www-data views/.cache
```

- **Development**: Start building your application by creating controllers, views, and models within the src directory.

- **Testing**: Nebula includes a testing suite powered by PHPUnit. Add your tests under the tests directory and run them with: `./bin/test` or `composer run-script test` or `./vendor/bin/phpunit tests`

- **Deployment**: Once your application is ready for deployment, configure your web server to point to the public directory as the document root.

<s>For more detailed instructions and documentation, please refer to the <a href='#'>Nebula Documentation</a></s>


### Contributing

Contributions to Nebula are welcome! If you find any issues or have suggestions for improvements, please open an issue or submit a pull request. 


### License

This project is licensed under the <a href='https://github.com/your-username/nebula/blob/main/LICENSE'>MIT License</a>.


### Acknowledgements

We would like to express our gratitude to the following open-source projects that have inspired and contributed to Nebula:

- Symfony
- Slim Framework
- Laravel


### Contact

For any inquiries or questions, please contact william.hleucka@gmail.com.

Let the Nebula framework guide you through the cosmos of web development, and create stellar applications that shine bright in the digital universe.


🇨🇦 Made in Canada
