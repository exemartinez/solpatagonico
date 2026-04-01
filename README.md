# Sol Patagónico
This project is a distribution management system focused on the commercial operation of selling products to hotel clients in Patagonia. It is not just a customer list or an invoicing tool. The system covers the full internal workflow: clients and branches, suppliers, products and categories, stock control, order entry, stock reservation, packaging, delivery notes (`remitos`), delivery reception, invoicing, reports, users, groups, permissions, logs, and personal data management.

## Introduction
What makes this repository especially valuable is that it preserves both sides of the project:

- the application source code in `Source Code/solpatagonico`
- the initial database in `Source Code/Scripts SQL/dump_inicial.sql`
- the full project documentation in `Documentacion`, including requirements, UML diagrams, test cases, project planning, risk tracking, user manual, change requests, and final presentation material

This was a serious 2009 distribution management system that we built with the tools, assumptions, and engineering culture of its time. 
We used PHP 5, MySQL, ADOdb, xajax, server-rendered forms, and direct SQL to solve a real operational problem for a regional distributor. 
It was designed around explicit business states for orders, stock, shipping, and invoicing, and it was delivered with a documentation discipline that is better than many modern internal delivery tools.
It shows its age in security practices, browser assumptions, and monolithic page structure. But it also shows something that is easy to forget in 2026: a lot of durable business software was built by small teams using simple stacks, strong domain understanding, and rigorous documentation. 
This repository is a very good example of that.

## What the system does

From the code, the SQL schema, and the user manual, the operational flow is clear:

1. Users log in with credentials and can choose a horizontal or vertical menu layout.
2. The business maintains master data for clients, client branches (`sucursales`), zones, suppliers, products, product categories (`rubros`), account types, transport types, and vehicles.
3. Orders (`pedidos`) are entered for a client and branch with one or more products.
4. A reservation task evaluates available stock and moves order items to either:
   - pending stock
   - reserved
5. Packaging (`embalaje`) advances reserved items and discounts real stock while releasing reserved stock.
6. Delivery notes (`remitos`) group items for transport and assign a driver and vehicle.
7. Delivery reception marks shipped items as delivered.
8. Invoicing assigns invoice number and invoice date to delivered delivery notes.
9. Reporting modules summarize order states, customer rankings, product rankings, suppliers by product, products by supplier, pending stock, and invoiceable delivery notes.

The state machine is encoded directly in the data model. `estado_item` and `estado_pedido` include values such as `Pendiente stock`, `Reservado`, `En curso`, `Entregado`, `Facturado`, and `Baja`. That gives the application a simple transactional backbone without needing a separate workflow engine.

## How it was built

This is a classic PHP 5 + MySQL 5 web application from the late 2000s. (LAMP)

The original deployment assumptions are explicit in the manual:

- Apache
- PHP 5.0
- MySQL 5.0
- `register_globals = On`
- Internet Explorer 6 or newer
- Windows-first operational expectations, although Apache on Linux was also considered valid

Technically, the application is organized around server-rendered PHP pages. Each business area lives in a single section file under `sections/`, for example:

- `clientes.php`
- `productos.php`
- `stock.php`
- `pedidos.php`
- `embalaje.php`
- `remito.php`
- `entrega.php`
- `facturacion.php`

Those files combine:

- request handling
- validation
- SQL execution
- HTML rendering
- some inline JavaScript

That was common and practical for us at the time. The priority was getting a usable business system online quickly, with low hosting complexity and minimal moving parts.

There is also a custom in-house framework layer under `_lib/1.1`, plus bundled third-party libraries such as:

- ADOdb for database access
- xajax for AJAX-style interactions (the first few steps into modern Javscript libs, before even TypeScript)
- JavaScript calendar and menu components

The framework provides reusable helpers for forms, authentication, validation, paging, logging, and configuration. In other words, this was not written as raw PHP scripts from scratch. It sits on top of a small reusable platform that E4System appears to have used across projects.

## Why the architecture looks like this

Seen from 2026, many decisions here are very characteristic of 2007-2009 web development:

### 1. Server-rendered pages everywhere

This predates the modern SPA era. The browser was mainly a thin client. Most logic lived on the server, and each screen was generated as HTML by PHP. That reduced deployment complexity and matched the skills and tools widely available at the time.
We essentially made a thin layered "three-layers" architecture, that was the trendy architecture at the time.

### 2. Business modules mapped one-to-one to files

Each use case or admin area is basically its own PHP page. That makes the code repetitive by modern standards, but it made the system understandable for our small team: if I needed to modify stock, I went to `stock.php`; if I needed invoicing, I went to `facturacion.php`.

### 3. Heavy use of relational state

Instead of abstract service layers, queues, or domain events, the workflow is implemented directly with database tables and status IDs. The reservation task (`tarea_reservar.php`) and order update task (`tarea_actualizar_pedido.php`) are simple, explicit scripts that move rows from one state to another. For a distribution business with finite stock, this was a sensible way to keep the operational logic visible and auditable.

### 4. Frames and iframes for layout composition

The main screen loads content into an `iframe`, and the user can choose horizontal or vertical menu layouts. In 2009 that was still a pragmatic way to keep navigation persistent while replacing only the main work area.

### 5. xajax instead of modern frontend frameworks

Autocomplete-like lookups and dynamic form behavior are handled with xajax responses that send JavaScript back to the browser. That was a real productivity tool in the pre-jQuery-app / pre-SPA period. It let PHP developers create interactive forms without building a separate frontend application.

### 6. Broad compatibility over strict correctness

The manual explicitly asks for `register_globals = On`, and the code relies on request variables being available directly. That was already becoming a risky practice even then, but it was still present in many small and mid-sized PHP systems because it reduced ceremony and sped up development.

### 7. MyISAM and straightforward SQL

The schema uses MyISAM tables and handwritten SQL. This fits the era: direct SQL was normal, ORMs were optional, and transactional guarantees were often traded for simplicity and familiarity, especially in internal business systems.

## The process behind the code
Our team consciously and consistently chose:

- waterfall (`cascada`) as lifecycle model
- UML as modeling language (which was edgy at the time)
- ERS / SRS-style requirements specification
- WBS and effort estimation
- use case specifications
- activity, sequence, collaboration, class, architecture, deployment, and entity-relationship diagrams
- test case documentation
- project plan, risks, minutes, and change management
- user manual and final presentation artifacts

The methodology document is explicit: the team selected a waterfall lifecycle, with document-based handoffs between analysis, design, construction, testing, and closure. 
From a 2026 perspective that may sound rigid, but back in that time: it made sense.
This project is the product of an academic software engineering effort where process maturity was part of the deliverable.

Some project metrics of sorts to have into account:

- 73 source files
- 18 directories
- 22,305 lines of code
- 20,017 physical executable lines
- 1,544 McCabe complexity points

By 2009, these kind of measurements where dictated by the manual.

## Design priorities

The design priorities were roughly these:

### Operational coverage first

The system aims to cover the entire daily workflow of a distributor, not just one isolated area. The breadth of modules confirms that the main value proposition was integration of operations.

### Low-cost deployment

PHP + Apache + MySQL on commodity hardware, with browser-based access and no dedicated client install beyond a standard browser.
Cheap, fast and streamlined for 2009.

### Admin control and traceability

The repository includes user groups, permissions, actions, logs, maintenance mode, password aging, wrong-password blocking, inactive account expiration, and password history tables.

### Documentability

Extensive and comprehensive for the early 2000's. Again, this is an academic work, and its main goal was to present a whole software development cycle.

## 2026 Retrospective 
Even with all the dated implementation details, I think several ideas are still solid:

- model the business as explicit states and transitions
- keep master data, operations, and reporting in one coherent system
- make roles and permissions part of the product from the beginning
- produce user documentation, not just code
- define requirements and test cases before calling the project complete

In fact, the most durable part of this repository is in fact not the PHP itself, but the combination of:

- a clear business workflow
- a coherent data model
- evidence of deliberate engineering process

And this is more relevant than ever almost 20 years later with the advent of Spec Driven Development (DDD)

