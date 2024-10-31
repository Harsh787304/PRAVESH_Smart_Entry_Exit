The idea is to use the Arduino Cloud Platform for programming and monitor the student details. A RFID scanner will detect rfid cards, and upon detection the details of the students will be updated into the database. This setup will be installed at both the entry/exit points of the area. Whenever a person crosses the RFID sensor at the time of exit/entry, the count will be decremented/incremented.
RFID tags will be distributed among the students, and a RFID scanner i.e. our module will be placed at the entry/exit gates. The rfid scanner will detect a person has tapped his rfid card and on tap the system automatically fills the time, date, Name, Roll Number, Room Number and other details. On tap, a Form would also be sent on mail. The user has to fill it for the purpose of visit.
The main microcontroller board we chose for this project is the Arduino UNO R4 WiFi. Additionally, the built-in ESP32 will connect to a hotspot, providing internet access to the device.

Project Details
We are from a college focused on  Information Technology, to the extent that even it's name is Indian Institute of INFORMATION TECHNOLOGY, Nagpur. But we figured that there were still some things which could benefit from it's technological aspect. One such prominent example we witnessed was present at our college gate.
Currently, our college uses a notebook and a pen in order to track the entry and exit details of the students coming in and going out of the campus. This process of manual entry is time-consuming and prone to errors.
Especially, at the weekends or during rush time, long queues can be seen at the gate which further results in delays and frustration among the students. Just ask a person to find a name in the list and it's a nightmare. Sometimes even the students themselves struggle to find their own entry. Moreover, it's difficult to manage all the records accurately, what if any notebook is lost?
We felt, this problem deserved not just a solution but a revolution. So after many long discussions and brainstorming on the implementation of the idea, we finally got it working. PRAVESH (Hindi Translation of Entry) is a RFID enabled Smart Entry Exit System which is capable of solving most if not all of the problems mentioned above.
Hence, we believe that PRAVESH will bring a revolution in IIIT Nagpur and will succeed in solving the problem of the majority of the students.

Project Highlights
➤For Students
RFID Cards enable Hassle Free and Faster Entry & Exit to students.
No More Manual Entry.
Seamless Casual Visits
Promotes Accountability among Students
Increased Security and Tracking

➤For Administration
Paperless Operations
Efficient Record Management
Automated Notifications & Alerts
Real-Time Reporting & Analytics
Access Control & Management
Improved Campus Security

Working of the RFID Wi-Fi Access Control System
This project functions as an RFID-based access control system that uses an ESP32 to connect to a server, send card data, and provide real-time feedback using LEDs and a buzzer. The system operates as follows:
 
Setup and Initialization: Upon powering on, the ESP32 connects to the configured Wi-Fi network. If connected successfully, the green LED turns on to indicate network readiness. Meanwhile, the RFID reader (MFRC522) and other output components (LEDs and buzzer) are also initialized.
 
Wi-Fi Status Check: In each loop iteration, the ESP32 checks its Wi-Fi connection status. If disconnected, the red LED lights up to signal this. Once Wi-Fi reconnects, the red LED turns off and the green LED turns back on.
 
RFID Tag Detection: The RFID reader continuously scans for new RFID tags. When a tag is detected, the reader retrieves the UID, a unique identifier for each tag. This UID is converted to a string for easy transmission.
 
Visual and Audio Feedback: Upon detecting an RFID tag, the system lights up the blue LED and activates the buzzer. This provides an immediate indication to the user that their tag has been scanned.
 
Data Transmission to Server: The UID is sent to the server using an HTTP POST request. The server URL is predefined, and the UID data is posted to the endpoint /dht11_project/test_data.php. The server response status and body are printed to the Serial Monitor for debugging and confirmation.
 
Completion and Reset: After the UID is successfully sent, the system deactivates the blue LED and buzzer and reactivates the green LED, readying the system for the next scan. The RFID reader halts communication with the card to conserve power until the next tag is detected.
 
Email functionality: During the Data Transmission to Server step , the uid is also matched to the email of respective person and an form is sent to the registered email id of the person with the reason for visit and return time estimate thing , after the user fills that and submits the form , it automatically updates at the database .
 
Deadline Mail: If till before 1hr of the user defined deadline time , the person is not inside campus , a deadline mail is sent to the persons mail id saying that deadline time is reaching and only 1 hr left , you have to be in a hurry for campus.
Delay and Repeat: A short delay prevents the system from reading the same card multiple times too quickly. The system then loops back to check for Wi-Fi status and new RFID tags, maintaining continuous operation.
