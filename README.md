# SN Toolbox : Framework

The SN Toolbox aims to provide classic tools in PHP development that will often be needed, without having to implement them over and over.

This particular package brings the Man-Machine Interface framework : MVC-oriented framework that uses Twig as template engine

# Download

The easiest way to download the core tools is through Composer. Simply add the following to your composer requirements, where "~1.0" can be replaced by any version you need :

```
"sntools/framework": "*"
```

# The idea behind the interface framework

In order to improve maintenance of applications, one of the concepts added to developpement is MVC : Model View Controller. Many frameworks implement it.
However, most of the time (if not always) the MVC concept covers all the layers of the application, solidifying the relation between
logic, interface and data access. This can cause problems when an application can possibly need multiple distinct interfaces.

One such case is a server-side application (with a RESTful API) that needs to have a website as one interface, and a distributed mobile application as another.
Instead of having to develop 2 whole applications, we can simply use the MVC concept solely within the Man-Machine Interface layer.
In such case, the Model of the MVC concept will talk to the deeper logic layer of the server-side application.

This is the idea of MVC this framework is based on. There is no data access concept as it will be the responsability of the deeper application layers, but
the framework provides both the Views (Twig templates, Response object) and Controllers, while the Models are implemented by the logic layers of the application.

# Usage

coming soon

# API Reference

To generate the documentation, use the apigen.neon file to generate it in a "docs" folder

```
> apigen generate
```

# Testing

Coming soon in /tests subfolder...

# Contributors

Samy Naamani <samy@namani.net>

# License

MIT