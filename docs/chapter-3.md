# CHAPTER THREE
## RESEARCH METHODOLOGY

### 3.1 Introduction
This chapter presents the research methodology adopted for the design and implementation of the Automated Warehouse Management System for ELECTRO MALL. The methodology encompasses the research design, data collection methods, system design approach, system architecture, data flow diagrams, flowcharts, and system requirements. The chapter provides a comprehensive framework that guided the development of the proposed system, ensuring methodological rigour and practical applicability.

### 3.2 Research Design
This study adopts a mixed-methods research design, combining qualitative and quantitative approaches to address the research objectives. The qualitative component involves the observation of existing warehouse practices at ELECTRO MALL and analysis of relevant literature, while the quantitative component encompasses system performance metrics and comparative analysis of manual versus automated processes. The research design follows the System Development Life Cycle (SDLC) model, specifically the iterative and incremental approach, which allows for progressive system refinement based on feedback and testing results.

The SDLC approach was selected because it provides a structured framework for system development, encompassing requirements analysis, design, implementation, testing, deployment, and maintenance phases (Pressman, 2014). The iterative nature of the methodology enables continuous improvement of the system through repeated cycles of development and evaluation. This approach is particularly suitable for software development projects where requirements may evolve during the development process.

The research also incorporates elements of action research, as the system is developed in close collaboration with ELECTRO MALL warehouse personnel, with their feedback informing design decisions and system refinements. This participatory approach ensures that the resulting system addresses genuine operational needs and is compatible with existing workflows and organizational culture.

### 3.3 Data Collection Methods

#### 3.3.1 Primary Data Collection
Primary data were collected through direct observation of warehouse operations at ELECTRO MALL. The observation method involved systematic documentation of existing inventory management practices, including receiving procedures, storage arrangements, stock movement tracking, order fulfilment processes, and record-keeping methods. Observations were conducted over a period of four weeks, covering different operational shifts and peak activity periods to capture the full range of warehouse activities.

Structured interviews were conducted with key warehouse personnel, including the warehouse manager, inventory clerks, and receiving staff. The interviews focused on understanding current operational challenges, information requirements, and desired system functionalities. A semi-structured interview guide was developed to ensure consistency while allowing for the exploration of emergent themes and specific concerns raised by participants.

Additionally, time-motion studies were conducted to quantify the duration of key warehouse activities under the existing manual system. These measurements provided baseline performance metrics against which the automated system could be evaluated, including receiving time per shipment, order picking time, inventory counting time, and order processing time.

#### 3.3.2 Secondary Data Collection
Secondary data were gathered through comprehensive review of academic literature, industry reports, and technical documentation related to warehouse management systems, barcode technology, QR code applications, and automated inventory management. Online databases including Google Scholar, IEEE Xplore, and ResearchGate were searched for relevant publications from 2016 to 2026. Industry reports from organizations such as the Material Handling Institute (MHI) and the Warehousing Education and Research Council (WERC) provided contemporary insights into warehouse automation trends and best practices.

Technical documentation for Python programming libraries, database management systems, and barcode/QR code generation tools were consulted to inform system design decisions. The Python Software Foundation documentation, Django framework documentation, and library-specific guides for `qrcode`, `pyzbar`, and `SQLite` were particularly valuable in guiding the technical implementation.

### 3.4 System Design Approach
The system design follows the object-oriented design paradigm, employing principles of encapsulation, inheritance, and polymorphism to create a modular, maintainable, and extensible software architecture. The design process was guided by the Unified Modeling Language (UML) notation, which provides standardized visual representations of system structure and behaviour.

The design approach emphasizes separation of concerns, with distinct modules handling user interface presentation, business logic processing, data persistence, and external system integration. This modular architecture facilitates independent development and testing of system components, simplifies maintenance activities, and supports future system enhancements.

The system was designed with scalability in mind, accommodating potential growth in inventory volume, transaction frequency, and user base. Database schema design incorporates normalization principles to minimize data redundancy while ensuring data integrity. The application architecture supports both single-user and multi-user deployment scenarios, with appropriate concurrency control mechanisms.

### 3.5 System Architecture
The proposed Automated Warehouse Management System adopts a three-tier client-server architecture, comprising the presentation tier, application tier, and data tier. This architectural pattern separates user interface concerns from business logic and data management, enhancing system maintainability and scalability.

#### 3.5.1 Presentation Tier
The presentation tier encompasses all user interface components through which warehouse personnel interact with the system. The user interface is implemented as a desktop application using Python's `Tkinter` library, which provides native cross-platform graphical user interface capabilities. The interface design follows principles of usability and accessibility, with intuitive navigation, clear visual feedback, and context-sensitive help features.

The presentation tier includes specialized interfaces for different user roles, including warehouse administrators, inventory clerks, receiving staff, and order pickers. Role-based access control ensures that users can only access functionalities relevant to their responsibilities, enhancing system security and simplifying the user experience.

#### 3.5.2 Application Tier
The application tier contains the core business logic of the warehouse management system, implemented in Python. This tier processes user requests, enforces business rules, coordinates data operations, and manages barcode/QR code generation and scanning functionality. Key application modules include:

- **Inventory Management Module**: Handles product registration, stock updates, location tracking, and inventory inquiries. This module implements algorithms for stock level monitoring, reorder point calculation, and inventory valuation.
- **Barcode/QR Code Module**: Manages the generation of unique identifiers for products, locations, and transactions. The module integrates Python libraries (`qrcode`, `pyzbar`) for code generation and decoding, supporting both barcode and QR code formats.
- **Transaction Processing Module**: Coordinates warehouse activities including receiving, put-away, picking, packing, and shipping. The module maintains audit trails of all inventory movements and updates stock levels in real-time.
- **Reporting Module**: Generates operational reports including inventory status reports, movement history, stock valuation, and performance analytics. Reports can be exported in various formats including PDF and Excel.

#### 3.5.3 Data Tier
The data tier manages persistent storage of all system data using SQLite, a lightweight, serverless database engine embedded within the Python application. SQLite was selected for its zero-configuration deployment, cross-platform compatibility, and sufficient performance for the anticipated data volumes at ELECTRO MALL. The database schema includes tables for products, categories, locations, inventory transactions, users, and system configuration.

For production deployment scenarios requiring multi-user concurrent access, the system architecture supports migration to client-server database systems such as MySQL or PostgreSQL with minimal code modification. The data access layer is implemented using Python's `sqlite3` module, with parameterized queries to prevent SQL injection attacks and ensure data integrity.

*Figure 3.1: Three-Tier System Architecture for the Automated Warehouse Management System*

### 3.6 Data Flow Diagram (DFD)
Data Flow Diagrams are used to represent the flow of data through the warehouse management system, illustrating how information moves between external entities, processes, and data stores. The following DFD levels are presented to provide comprehensive understanding of system data flows.

#### 3.6.1 Context Diagram (Level 0 DFD)
The context diagram presents the warehouse management system as a single process interacting with external entities. The external entities include: **Suppliers** (providing goods for receipt), **Customers** (receiving shipped orders), **Warehouse Manager** (overseeing operations and generating reports), and **Inventory Staff** (performing daily warehouse activities). Data flows between these entities and the central WMS process include purchase orders, receiving reports, shipment confirmations, inventory inquiries, and management reports.

*Figure 3.2: Context Diagram (Level 0 DFD) showing system boundaries and external data flows*

#### 3.6.2 Level 1 DFD
The Level 1 DFD decomposes the central WMS process into six major sub-processes: 
1. **Receiving Management**, which processes incoming shipments and updates inventory; 
2. **Storage Management**, which manages product locations and warehouse layout; 
3. **Inventory Tracking**, which maintains real-time stock levels and movement records; 
4. **Order Processing**, which handles customer order fulfilment; 
5. **Reporting and Analytics**, which generates operational and management reports; and 
6. **System Administration**, which manages user accounts, permissions, and system configuration.

Each sub-process interacts with the central database (data store) and exchanges data with relevant external entities. For example, the Receiving Management process receives shipment data from Suppliers, validates against purchase orders, generates barcode/QR code labels for received items, and updates inventory records in the database. The Inventory Tracking process processes scan data from Inventory Staff, updating location and quantity information in real-time.

*Figure 3.3: Level 1 Data Flow Diagram showing six major sub-processes and data flows*

#### 3.6.3 Level 2 DFD - Receiving Process
The Level 2 DFD for the Receiving Management process further decomposes the receiving workflow into detailed processes: 
- **(2.1) Shipment Verification**, which validates incoming shipments against purchase orders; 
- **(2.2) Item Inspection**, which checks received items for damage and quantity accuracy; 
- **(2.3) Barcode/QR Code Generation**, which creates unique identifiers for received items; 
- **(2.4) Label Printing**, which generates physical labels for affixing to products; and 
- **(2.5) Inventory Update**, which records received quantities and locations in the database.

### 3.7 Flowchart of the System
System flowcharts provide visual representations of the sequential logic and decision points within key warehouse processes. The following flowcharts illustrate the primary operational workflows of the automated warehouse management system.

#### 3.7.1 Receiving Process Flowchart
The receiving process begins when a shipment arrives at the warehouse dock. The process flow includes: 
1. Shipment arrival notification and documentation review; 
2. Purchase order verification against delivery note; 
3. Physical inspection of items for quantity and condition; 
4. **Decision point:** if items match order and pass inspection, proceed to labelling; if discrepancies exist, initiate exception handling; 
5. Barcode or QR code generation for each item or pallet; 
6. Label printing and affixing; 
7. System-directed put-away location assignment based on product characteristics and warehouse layout; 
8. Physical movement to assigned location; 
9. Location confirmation scan; and 
10. Inventory record update with received quantities, locations, and timestamps.

*Figure 3.4: Flowchart of the Receiving Process with decision points and exception handling*

#### 3.7.2 Order Picking Process Flowchart
The order picking process flow includes: 
1. Order receipt from sales system; 
2. Order validation and availability check; 
3. Pick list generation with optimized pick sequence based on warehouse layout; 
4. Picker assignment and pick list transmission to mobile device; 
5. Picker navigation to first pick location; 
6. Item location scan verification; 
7. Item barcode/QR code scan for product verification; 
8. Quantity confirmation and pick recording; 
9. **Decision point:** if more items on pick list, return to step 5; if pick list complete, proceed to packing; 
10. Order consolidation and packing; 
11. Shipping label generation; and 
12. Order status update and inventory deduction.

*Figure 3.5: Flowchart of the Order Picking Process with loop-back mechanism for multi-item orders*

#### 3.7.3 Inventory Counting Process Flowchart
The inventory counting (cycle counting) process flow includes: 
1. Count schedule generation based on ABC classification or random selection; 
2. Count assignment to inventory staff; 
3. Navigation to designated count location; 
4. Location scan verification; 
5. Sequential item scanning using barcode/QR code scanner; 
6. System comparison of scanned quantities against expected quantities; 
7. **Decision point:** if quantities match, count is confirmed; if variance exists, initiate recount procedure; 
8. Variance investigation and approval for inventory adjustment; 
9. Inventory record update; and 
10. Count completion and performance reporting.

#### 3.7.4 Use Case Diagram (UML)
The Use Case Diagram presents the functional requirements of the Automated Warehouse Management System from the perspective of system users (actors) and the functionalities (use cases) they interact with. The diagram identifies seven actors: Administrator, Warehouse Manager, Inventory Clerk, Receiving Staff, Picker, Supplier, and Customer, each with specific roles and responsibilities within the warehouse ecosystem.

The Administrator actor has full system access, managing user accounts, configuring system parameters, and overseeing all operational functions. The Warehouse Manager focuses on reporting, analytics, and operational oversight. Inventory Clerks perform inventory tracking, stock level updates, and cycle counting activities. Receiving Staff handle shipment processing and label generation. Pickers execute order fulfilment tasks including item scanning and picking.

External actors include the Supplier, who provides shipment data and receives receiving reports, and the Customer, who places orders and receives shipment confirmations. The diagram also illustrates `<<include>>` relationships, where certain use cases are mandatory components of others (e.g., generating barcode/QR code labels is included in receiving shipments), and `<<extend>>` relationships, where optional functionalities extend base use cases.

*Figure 3.6: UML Use Case Diagram showing actors and system functionalities*

#### 3.7.5 Class Diagram (UML)
The Class Diagram presents the static structure of the Automated Warehouse Management System, illustrating the classes, their attributes, methods, and the relationships between them. The diagram follows the Unified Modeling Language (UML) notation, with classes represented as three-part boxes containing the class name, attributes, and methods.

The core classes include `User` (managing authentication and role-based access), `Product` (representing electronic goods with SKU, barcode, and QR code attributes), `Category` (classifying products into logical groups), `Location` (representing warehouse storage positions with hierarchical addressing), `Inventory` (tracking stock levels by product and location), `Transaction` (recording all inventory movements with audit trail information), `BarcodeGenerator` (handling barcode and QR code generation and printing), `Order` and `OrderItem` (managing customer orders and line items), and `ReportGenerator` (producing operational and management reports).

Relationships between classes include associations (solid lines with cardinality indicators), where one class is associated with another (e.g., Product is associated with Category in a many-to-one relationship), and dependencies (dashed lines with `<<uses>>` stereotype), where one class utilizes the functionality of another (e.g., Product uses BarcodeGenerator for code generation). Cardinality notations indicate one-to-one (1:1), one-to-many (1:*), and many-to-many (*:*) relationships between classes.

*Figure 3.7: UML Class Diagram showing system classes, attributes, methods, and relationships*

#### 3.7.6 Sequence Diagram - Order Processing
The Sequence Diagram illustrates the dynamic interaction between system objects during the order processing workflow. The diagram shows the chronological sequence of messages exchanged between five lifelines: Customer, Sales Staff, WMS System, Database, and Picker. Each vertical dashed line represents the lifespan of an object, while horizontal arrows represent messages passed between objects.

The sequence begins with the Customer placing an order (Message 1), which is relayed by Sales Staff to the WMS System (Message 2). The system queries the Database to check stock availability (Message 3) and receives stock data in return (Message 4). Upon confirmation, the order is confirmed to the Customer (Messages 5-6), stock is reserved in the Database (Messages 7-8), and a pick list is generated and transmitted to the Picker (Message 9). The Picker scans and picks items (Message 10), the Database is updated (Messages 11-12), and the order completion is confirmed (Message 13).

Solid arrows represent synchronous messages (requests), while dashed arrows represent return messages (responses). Activation boxes (colored rectangles on lifelines) indicate periods when objects are actively processing. This diagram is essential for understanding the temporal dependencies and coordination requirements between system components during order fulfilment.

*Figure 3.8: UML Sequence Diagram for Order Processing workflow*

#### 3.7.7 Activity Diagram - Inventory Counting Process
The Activity Diagram presents the Inventory Counting Process using UML activity diagram notation with swimlanes. Swimlanes organize activities by the responsible actor or system component, with three swimlanes representing the System, Inventory Clerk, and Manager. This visualization clarifies role responsibilities and handoffs throughout the counting workflow.

The System swimlane contains automated activities including count schedule generation, count assignment to inventory clerks, quantity comparison with expected values, and inventory record updates. The Inventory Clerk swimlane contains manual activities performed by warehouse staff, including receiving count assignments, navigating to count locations, scanning location and item barcodes/QR codes, and entering physical count quantities. The Manager swimlane contains supervisory activities including variance review and approval for inventory adjustments.

The diagram includes two decision points: 
1. **Match verification**, where counted quantities are compared against expected quantities, with matching counts proceeding to confirmation and non-matching counts flagged for variance investigation; and 
2. **Manager Approval**, where proposed inventory adjustments are reviewed and either approved (proceeding to record update) or rejected (requiring recount or further investigation). 

The swimlane format clearly delineates responsibilities and ensures accountability at each process stage.

*Figure 3.9: UML Activity Diagram for Inventory Counting Process with swimlanes*

#### 3.7.8 Entity Relationship Diagram (ERD)
The Entity Relationship Diagram presents the logical database design for the Automated Warehouse Management System, illustrating the entities (tables), their attributes, and the relationships between them. The diagram follows Chen's notation conventions, with rectangles representing entities, attributes listed within entity boxes, and lines connecting related entities with cardinality indicators.

The database schema comprises eleven entities: `User` (storing authentication and role information), `Category` (classifying products), `Product` (the core product catalogue with barcode/QR code attributes), `Supplier` (vendor information), `Location` (warehouse storage positions), `Inventory` (current stock levels by product and location), `Transaction` (audit trail of all inventory movements), `PurchaseOrder` (incoming shipment orders), `POItem` (line items within purchase orders), `Order` (customer sales orders), and `OrderItem` (line items within customer orders).

Primary keys (PK) are indicated in red bold text, uniquely identifying each record within an entity. Foreign keys (FK) are indicated in blue text, establishing relationships between entities. Cardinality notations (1 and *) indicate the nature of relationships: one-to-one (1:1), one-to-many (1:*), and many-to-many (*:*) associations. For example, the relationship between Category and Product is one-to-many (one category can have many products), while the relationship between Product and Inventory is one-to-many (one product can exist in multiple locations with different quantities).

The database design follows third normal form (3NF) normalization principles, minimizing data redundancy while ensuring data integrity through foreign key constraints. The schema supports all core warehouse operations including receiving, storage, tracking, picking, and reporting, while maintaining comprehensive audit trails for accountability and compliance.

*Figure 3.10: Entity Relationship Diagram (ERD) showing database schema with entities, attributes, and relationships*

### 3.8 System Requirements

#### 3.8.1 Functional Requirements
The functional requirements define the specific behaviours and capabilities that the system must exhibit to satisfy user needs. The key functional requirements of the Automated Warehouse Management System include:

- **Product Management:** The system shall support the registration of electronic goods with attributes including product name, category, brand, model, specifications, unit of measure, and supplier information. Each product shall be assigned a unique identifier encoded in barcode or QR code format.
- **Inventory Tracking:** The system shall maintain real-time inventory levels for all products, tracking quantities on hand, quantities reserved for orders, and available quantities. The system shall record all inventory movements with timestamps, user identification, and transaction types.
- **Barcode/QR Code Operations:** The system shall generate barcode and QR code labels for products, locations, and pallets. The system shall support scanning-based verification during receiving, put-away, picking, and shipping operations. The system shall decode scanned codes and retrieve corresponding product information from the database.
- **Location Management:** The system shall manage warehouse storage locations with hierarchical addressing (zone, aisle, rack, shelf, bin). The system shall support system-directed put-away based on product characteristics, storage requirements, and warehouse layout optimization.
- **Receiving Management:** The system shall process incoming shipments, validate against purchase orders, generate receiving reports, and update inventory records. The system shall support partial receipts, over-receipts, and exception handling for damaged or incorrect items.
- **Order Fulfilment:** The system shall process customer orders, generate pick lists with optimized pick sequences, track picking progress, and manage order packing and shipping. The system shall update inventory levels upon order completion.
- **Reporting:** The system shall generate standard reports including inventory status reports, stock movement reports, receiving reports, shipping reports, and performance analytics. Reports shall support date range filtering, product filtering, and export to common formats.
- **User Management:** The system shall support multiple user accounts with role-based access control. User roles shall include Administrator, Warehouse Manager, Inventory Clerk, Receiving Staff, and Picker. The system shall maintain audit logs of user activities.

#### 3.8.2 Non-Functional Requirements
Non-functional requirements specify the quality attributes and constraints that the system must satisfy:

- **Performance:** The system shall process barcode/QR code scans within 2 seconds, including database lookup and screen update. The system shall support concurrent operation by up to 10 users without performance degradation.
- **Reliability:** The system shall achieve 99.5% uptime during operational hours. Data integrity mechanisms shall prevent loss of inventory transactions due to system failures. Automatic backup functionality shall protect against data loss.
- **Usability:** The user interface shall be intuitive and require minimal training for warehouse staff. Error messages shall be clear and actionable. The system shall provide context-sensitive help and visual feedback for all operations.
- **Security:** User authentication shall require username and password. Passwords shall be stored using cryptographic hashing. Role-based access control shall restrict functionality based on user permissions. Audit logs shall record all data modifications.
- **Scalability:** The database design shall accommodate growth to 100,000 product records and 1,000,000 transaction records without performance degradation. The application architecture shall support migration to client-server deployment if required.
- **Portability:** The system shall run on Windows, macOS, and Linux operating systems. The database shall be portable without requiring schema modification.

#### 3.8.3 Hardware Requirements
The minimum hardware requirements for system deployment include: 
- Computer with dual-core processor (2.0 GHz or higher)
- 4 GB RAM (8 GB recommended)
- 100 GB available hard disk space
- USB ports for barcode scanner connectivity
- Network interface card for multi-user deployment
- Printer for label generation

**Recommended hardware includes:**
- Quad-core processor (3.0 GHz or higher)
- 8 GB RAM
- 500 GB SSD storage
- Dedicated barcode/QR code scanner (handheld or fixed-mount)
- Label printer (thermal transfer)
- UPS (uninterruptible power supply) for data protection.

#### 3.8.4 Software Requirements
The software requirements for system deployment include: 
- **Operating System:** Windows 10/11, macOS 10.15+, or Ubuntu 20.04+
- **Python:** 3.9 or higher
- **Database:** SQLite 3.35 or higher (embedded)
- **GUI Library:** Tkinter (included with Python standard library)
- **Python Libraries:** `qrcode` 7.3+, `pyzbar` 0.1.8+, `Pillow` 9.0+, and `openpyxl` 3.0+ (for Excel export). 

*Optional software for enhanced deployment includes MySQL 8.0+ (for multi-user scenarios) and report generation libraries.*

### 3.9 Methodology Summary
This chapter has presented the comprehensive methodology employed in the design and implementation of the Automated Warehouse Management System for ELECTRO MALL. The mixed-methods research design, combining qualitative observation and quantitative performance measurement, provides a robust framework for system development and evaluation. The three-tier client-server architecture ensures separation of concerns and supports future scalability. The functional and non-functional requirements establish clear specifications for system capabilities and quality attributes.

The SDLC-based development approach, combined with participatory action research elements, ensures that the resulting system addresses genuine operational needs while maintaining methodological rigour. The detailed system architecture, data flow diagrams, and process flowcharts provide a solid foundation for system implementation, which is presented in Chapter Four. The methodology emphasizes practical applicability, cost-effectiveness, and suitability for the specific operational context of ELECTRO MALL, addressing the research gaps identified in the literature review.
