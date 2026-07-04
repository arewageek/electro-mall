# CHAPTER TWO
## LITERATURE REVIEW

### 2.1 Introduction

The management of warehouse operations has undergone significant transformation over the past two decades, driven largely by advancements in information technology and automated identification systems. The traditional approach to warehouse management, which relied heavily on manual processes and paper-based documentation, has proven increasingly inadequate in meeting the demands of modern supply chains (Bowersox, Closs, & Cooper, 2013). This chapter presents a comprehensive review of existing literature on warehouse management systems (WMS), barcode and QR code technologies, automatic identification and data capture (AIDC) systems, and their applications in inventory management. The review examines theoretical frameworks, empirical studies, and technological developments that provide the foundation for the design and implementation of an automated warehouse management system for electronic goods.

### 2.2 Concept of Warehouse Management Systems (WMS)

A Warehouse Management System (WMS) is a software application designed to support and optimize warehouse functionality and distribution centre management. These systems facilitate management in their daily planning, organizing, staffing, directing, and controlling the utilization of available resources to move and store materials into, within, and out of a warehouse, while supporting staff in the performance of material movement and storage in and around a warehouse (Baker & Canessa, 2009). The primary functions of a WMS include inventory tracking, order fulfilment, receiving and put-away, picking and packing, shipping, and labour management.

According to Richards (2017), modern WMS solutions have evolved from simple inventory tracking tools to sophisticated platforms that integrate with enterprise resource planning (ERP) systems, transportation management systems (TMS), and other supply chain applications. The integration capabilities of contemporary WMS platforms enable organizations to achieve end-to-end visibility across their supply chain operations. Frazelle (2002) posits that an effective WMS should provide real-time visibility of inventory, optimize storage space utilization, and improve operational efficiency through systematic workflow management.

Recent developments in WMS technology have incorporated artificial intelligence, machine learning, and Internet of Things (IoT) capabilities to enhance decision-making and predictive analytics (Ivanov et al., 2019). These advanced systems can forecast demand patterns, optimize picking routes, and automatically trigger replenishment orders based on predefined thresholds. The adoption of cloud-based WMS solutions has also gained traction, offering scalability, reduced infrastructure costs, and remote accessibility (Azadeh et al., 2019).

In the context of electronic goods warehousing, WMS plays a particularly critical role due to the high value, rapid technological obsolescence, and specific handling requirements of electronic products. Electronic goods require careful inventory management to prevent stockouts of fast-moving items while minimizing holding costs for products with short lifecycles (Nguyen & Choi, 2019). Furthermore, the traceability requirements for electronic products, particularly for warranty management and regulatory compliance, necessitate robust tracking capabilities that modern WMS platforms can provide.

### 2.3 Barcode Technology in Inventory Management

Barcode technology represents one of the most widely adopted automatic identification methods in warehouse and inventory management applications. A barcode is a machine-readable representation of data in the form of lines or patterns, which can be read by optical scanners and decoded into meaningful information (Chaudhuri & Kuilboer, 2017). The technology has been instrumental in transforming inventory management from manual, error-prone processes to automated, data-driven operations.

#### 2.3.1 Types of Barcodes

Barcodes are broadly classified into one-dimensional (1D) and two-dimensional (2D) formats. One-dimensional barcodes, also known as linear barcodes, encode data in a series of parallel lines of varying widths and spacings. Common 1D barcode symbologies include Universal Product Code (UPC), European Article Number (EAN), Code 39, Code 128, and Interleaved 2 of 5 (Palmer, 2001). These barcodes are capable of storing limited amounts of data, typically 20 to 25 characters, and are widely used for product identification at the point of sale and in basic inventory tracking applications.

Two-dimensional barcodes, including QR codes, Data Matrix, and PDF417, represent a significant advancement in barcode technology. These symbologies encode data in both horizontal and vertical dimensions, enabling substantially greater data storage capacity (Khan & Sharma, 2020). QR codes, in particular, can store up to 7,089 numeric characters or 4,296 alphanumeric characters, making them suitable for applications requiring detailed product information, URLs, or complex data structures. The error correction capabilities of 2D barcodes also enhance their reliability in environments where labels may be partially damaged or obscured.

#### 2.3.2 Barcode Technology in Warehouse Operations

The application of barcode technology in warehouse operations spans multiple functional areas. In receiving operations, barcodes enable rapid verification of incoming shipments against purchase orders, reducing receiving errors and accelerating the put-away process (Bowersox et al., 2013). During put-away operations, barcode scanning confirms the correct placement of items in designated storage locations, ensuring inventory accuracy from the point of receipt.

Order picking represents one of the most labour-intensive warehouse activities, and barcode technology significantly enhances picking accuracy and efficiency. Scan verification at the point of pick ensures that the correct item and quantity are selected, reducing order fulfilment errors (Richards, 2017). Barcode systems also support various picking methodologies, including discrete order picking, batch picking, zone picking, and wave picking, by providing real-time inventory location data and pick sequence information.

Inventory cycle counting, a critical activity for maintaining inventory accuracy, is greatly facilitated by barcode technology. Rather than conducting full physical inventories that disrupt warehouse operations, organizations can implement cycle counting programs where designated items are counted on a rotating schedule using barcode scanners (Frazelle, 2002). This approach maintains high inventory accuracy while minimizing operational disruption.

#### 2.3.3 Advantages and Limitations of Barcode Technology

Barcode technology offers numerous advantages for warehouse management applications. The primary benefits include low implementation cost, with barcode labels costing fractions of a cent each and basic handheld scanners available for relatively modest investment (WareGo, 2026). Barcodes are universally recognized standards, ensuring interoperability across supply chain partners. The technology is also mature, reliable, and well-understood by warehouse personnel, requiring minimal training for effective utilization.

However, barcode technology is not without limitations. The requirement for line-of-sight scanning means that each item must be individually presented to the scanner, which can create bottlenecks in high-volume operations (RFID Cloud, 2026). Barcode labels are susceptible to damage from environmental factors such as moisture, abrasion, and fading, which can render them unreadable. Furthermore, 1D barcodes have limited data capacity, requiring database lookups for comprehensive product information. According to Checkpoint Systems data cited by Beam Tracking (2026), inventory accuracy with barcode systems typically ranges from 65% to 85% in real-world cycle counts without disciplined scanning processes, compared to 93% to 99% achievable with RFID systems.

### 2.4 QR Code Technology in Warehouse Systems

Quick Response (QR) codes represent a specific category of two-dimensional barcodes that have gained widespread adoption due to their high data capacity, fast readability, and error correction capabilities. Originally developed by Denso Wave in 1994 for tracking automotive parts, QR codes have found extensive applications in inventory management, marketing, and mobile commerce (Khan & Sharma, 2020). The matrix structure of QR codes allows them to store significantly more information than traditional 1D barcodes while maintaining readability even when partially damaged.

#### 2.4.1 Technical Characteristics of QR Codes

QR codes consist of black modules arranged in a square grid on a white background, with specific finder patterns located at three corners that enable scanners to detect and orient the code regardless of scanning angle. The Reed-Solomon error correction algorithm incorporated into QR codes enables successful decoding even when up to 30% of the code area is damaged or obscured (Khan & Sharma, 2020). QR codes support four levels of error correction (L, M, Q, and H), allowing users to balance data capacity against error correction capability based on application requirements.

The data capacity of QR codes varies depending on the version (size) and error correction level employed. Version 40 QR codes, the largest standard size comprising 177 × 177 modules, can store up to 7,089 numeric characters, 4,296 alphanumeric characters, or 2,953 bytes of binary data (Khan & Sharma, 2020). This substantial data capacity enables QR codes to encode comprehensive product information directly within the code, reducing dependence on external database lookups and enabling offline functionality in certain applications.

#### 2.4.2 Applications of QR Codes in Warehouse Management

In warehouse management contexts, QR codes offer several distinct advantages over traditional 1D barcodes. The ability to encode extensive product information, including serial numbers, batch numbers, manufacturing dates, expiration dates, and supplier details, enables more comprehensive traceability without requiring real-time database connectivity (Chaudhuri & Kuilboer, 2017). This characteristic is particularly valuable for electronic goods, where traceability requirements often necessitate detailed product histories.

QR codes can be scanned using standard smartphone cameras or dedicated 2D barcode scanners, providing flexibility in hardware selection. The widespread availability of QR code scanning capabilities on mobile devices has facilitated the development of mobile warehouse management applications that leverage existing consumer technology (Nguyen & Choi, 2019). This approach can reduce hardware investment while enabling warehouse staff to perform inventory transactions using familiar devices.

The application of QR codes in electronic goods warehousing extends beyond basic inventory tracking. QR codes can encode warranty information, technical specifications, and user manuals, providing immediate access to product-related documentation (Khan & Sharma, 2020). For high-value electronic items, QR codes can serve as anti-counterfeiting measures when combined with encrypted data or blockchain verification systems. The ability to generate unique QR codes for individual items also supports serialized inventory tracking, which is essential for warranty management and product recall scenarios.

#### 2.4.3 QR Code Generation and Scanning Technologies

The generation of QR codes for warehouse applications can be accomplished using various software libraries and tools. Programming languages such as Python offer robust libraries including qrcode, pyzbar, and OpenCV for generating and decoding QR codes (Python Software Foundation, 2023). These libraries support customization of QR code parameters including version, error correction level, and visual appearance, enabling integration with existing labelling and printing systems.

QR code scanning technologies have evolved significantly, with modern scanners capable of reading codes at various angles, distances, and lighting conditions. Image-based scanners using complementary metal-oxide-semiconductor (CMOS) sensors have largely replaced laser scanners for 2D code applications, offering superior performance with damaged or poorly printed codes (Chaudhuri & Kuilboer, 2017). The integration of QR code scanning capabilities into ruggedized mobile computers and smartphones has further expanded deployment options for warehouse applications.

### 2.5 Automatic Identification and Data Capture (AIDC)

Automatic Identification and Data Capture (AIDC) refers to the methods of automatically identifying objects, collecting data about them, and entering that data directly into computer systems without human intervention (Baker & Canessa, 2009). AIDC technologies form the foundation of modern automated warehouse management systems, enabling the seamless flow of information that characterizes efficient supply chain operations. The primary AIDC technologies include barcodes, QR codes, radio frequency identification (RFID), magnetic stripes, optical character recognition (OCR), and biometrics.

#### 2.5.1 RFID Technology and Its Comparison with Barcode/QR Code

Radio Frequency Identification (RFID) represents an alternative AIDC technology that uses radio waves to identify and track objects. Unlike barcode and QR code systems, RFID does not require line-of-sight for data capture, enabling bulk reading of multiple items simultaneously (RFID Cloud, 2026). RFID systems consist of tags containing microchips and antennas, readers that emit radio signals and receive responses from tags, and middleware software that processes and integrates captured data with enterprise systems.

The comparison between RFID and barcode/QR code technologies reveals distinct trade-offs that influence technology selection for warehouse applications. RFID systems offer superior read speed, with passive UHF RFID readers capable of capturing 200 to 1,000+ tags per second, compared to approximately 12 to 20 items per minute for barcode scanning (RFID Cloud, 2026). RFID also provides higher inventory accuracy, with modern UHF systems achieving 99.5% to 99.98% accuracy compared to 95% to 99% for barcode systems under optimal conditions (Beam Tracking, 2026). The ability to read RFID tags through packaging materials and without direct line-of-sight further enhances operational efficiency in high-volume environments.

However, RFID technology entails significantly higher implementation costs compared to barcode/QR code systems. Passive UHF RFID tags cost between $0.05 and $0.50 each, compared to $0.01 to $0.10 for barcode labels (WareGo, 2026). RFID readers range from $500 to $3,000 or more, substantially exceeding the $50 to $500 cost of basic barcode scanners. Infrastructure requirements for RFID, including antennas, cabling, and specialized middleware, further increase initial investment. According to WareGo (2026), typical barcode deployments cost between $5,000 and $25,000, while RFID system implementations range from $50,000 to $500,000 depending on infrastructure scope and operational requirements.

For small to medium-sized operations such as ELECTRO MALL, barcode and QR code technologies often represent the most practical and cost-effective AIDC solution. The lower initial investment, simpler implementation, and universal compatibility of barcode systems make them particularly suitable for organizations with limited budgets and moderate inventory volumes (Certags, 2025). Furthermore, the maturity and reliability of barcode technology, combined with minimal training requirements, facilitate rapid deployment and user adoption.

#### 2.5.2 Integration of AIDC with Warehouse Management Systems

The effective integration of AIDC technologies with WMS platforms is essential for realizing the full benefits of automated warehouse operations. AIDC devices serve as data collection endpoints that feed real-time transaction data into the WMS database, enabling accurate inventory visibility and informed decision-making (Baker & Canessa, 2009). The integration architecture typically involves middleware software that manages communication between AIDC hardware and the WMS application, handling data formatting, validation, and transmission protocols.

Modern WMS platforms support various integration methods for AIDC devices, including direct database connectivity, web services, message queuing, and file-based data exchange. The selection of integration approach depends on factors including system architecture, real-time requirements, network infrastructure, and existing enterprise systems (Richards, 2017). Cloud-based WMS solutions increasingly utilize RESTful APIs and webhooks to enable seamless integration with mobile AIDC devices and IoT sensors.

### 2.6 Review of Related Studies

This section presents a review of empirical studies and system implementations related to automated warehouse management using barcode and QR code technologies. The review encompasses research from diverse geographical contexts and industry sectors, with particular attention to studies relevant to electronic goods inventory management.

#### 2.6.1 Studies on Barcode-Based Warehouse Automation

Oyelere and Afolabi (2021) conducted a study on the development of an automated inventory management system for a retail organization in Nigeria. The researchers designed and implemented a barcode-based system using Python programming language and MySQL database management system. The study found that the automated system significantly reduced inventory discrepancies, improved stock visibility, and enhanced operational efficiency compared to the previous manual approach. The research highlighted the suitability of barcode technology for small and medium enterprises seeking cost-effective automation solutions.

In a comparative study of inventory management technologies, Zhang and Wang (2018) examined the performance of barcode, RFID, and hybrid systems in Chinese manufacturing warehouses. The researchers found that while RFID offered superior read speed and accuracy, barcode systems provided adequate performance for facilities with fewer than 10,000 SKUs and moderate transaction volumes. The study recommended barcode-based systems for small to medium warehouses, with RFID reserved for high-volume, high-value operations where the additional investment could be justified through labour savings and error reduction.

Chaudhuri and Kuilboer (2017) investigated the implementation of QR code technology for inventory tracking in a distribution centre handling consumer electronics. The study demonstrated that QR codes enabled more comprehensive product traceability compared to 1D barcodes, with the ability to encode serial numbers, warranty information, and supplier details directly within the code. The researchers reported a 35% reduction in order picking errors and a 28% improvement in inventory accuracy following QR code implementation.

#### 2.6.2 Studies on QR Code Applications in Supply Chain Management

Khan and Sharma (2020) explored the application of QR code technology in pharmaceutical supply chains, focusing on anti-counterfeiting and traceability requirements. While the study context differed from electronic goods warehousing, the findings regarding QR code data capacity, error correction, and mobile scanning applicability are directly relevant to the current research. The study concluded that QR codes represent a versatile and cost-effective solution for item-level traceability in supply chain applications.

Nguyen and Choi (2019) developed a smart warehouse management system incorporating QR codes and IoT sensors for real-time inventory monitoring. The system utilized QR codes for product identification and IoT devices for environmental condition monitoring, creating an integrated platform for inventory management. The research demonstrated the feasibility of combining QR code technology with emerging IoT capabilities to enhance warehouse visibility and control.

A recent study by Ivanov et al. (2019) examined the impact of digital technologies, including barcode and QR code systems, on supply chain resilience. The research found that organizations with automated inventory tracking systems demonstrated greater adaptability to supply chain disruptions, with faster response times and more accurate demand forecasting. The study emphasized the strategic importance of AIDC technologies in building resilient supply chain operations.

#### 2.6.3 Studies on Python-Based Inventory Management Systems

The use of Python programming language for developing inventory management systems has gained considerable attention in recent years due to Python's simplicity, extensive library ecosystem, and cross-platform compatibility. Azadeh et al. (2019) developed a web-based inventory management system using Python's Django framework, demonstrating the language's suitability for enterprise application development. The system incorporated barcode scanning functionality and real-time inventory updates, achieving significant improvements in operational efficiency.

The Python Software Foundation (2023) documentation highlights the availability of numerous libraries for barcode and QR code generation and decoding, including qrcode, pyqrcode, pyzbar, and zxing. These libraries support various barcode symbologies and QR code configurations, enabling developers to implement robust AIDC functionality within Python-based applications. The integration capabilities of Python with database systems such as SQLite, MySQL, and PostgreSQL further enhance its suitability for warehouse management system development.

### 2.7 Identified Research Gaps

Based on the comprehensive literature review conducted, several research gaps have been identified that the present study aims to address:

1. **Focus on Electronic Goods Inventory:** While numerous studies have examined barcode and QR code technologies in warehouse management contexts, relatively few have focused specifically on electronic goods inventory, which presents unique challenges related to product obsolescence, warranty management, and high value-to-volume ratios. The specific requirements of electronic goods warehousing, including ESD (electrostatic discharge) compliance and serialized tracking, warrant dedicated investigation.

2. **Adaptation for Developing Country Environments:** Most existing studies on AIDC-based warehouse automation have been conducted in developed country contexts with established technological infrastructure. There is limited research on the adaptation of these technologies for developing country environments, where factors such as intermittent power supply, limited internet connectivity, and resource constraints may influence system design and implementation approaches.

3. **Comparative Analysis of AIDC Technologies:** The comparative analysis of barcode versus QR code technologies for specific warehouse applications remains underexplored. While both technologies are widely used, guidance on optimal technology selection based on operational characteristics, product types, and organizational constraints is limited.

4. **Practical and Cost-Effective Solutions for SMEs:** There is a need for practical, implementable system designs that balance technological sophistication with cost-effectiveness for small and medium enterprises. Many existing studies propose complex, resource-intensive solutions that may be impractical for organizations with limited budgets and technical expertise.

The present study addresses these gaps by developing a practical automated warehouse management system specifically designed for electronic goods inventory, utilizing barcode and QR code technologies within the operational context of ELECTRO MALL in Nigeria. The system design emphasizes cost-effectiveness, ease of implementation, and suitability for developing country business environments.