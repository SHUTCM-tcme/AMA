# Acupuncture Manipulation Analysis (AMA) Ver.2.0

### Software copyright registration number of the People's Republic of China: 2021SR1241036

### Copyright owner: Shanghai University of Traditional Chinese Medicine

This software is mainly used to analyze the parameter information of acupuncture manipulation (AM) derived from the three-dimensional motion tracking system (Simi Motion Ver.8.5). Kinematic parameters of six finger joints can be analyzed by this software during the periodic AM (lifting-thrusting and twirling), and caculate the average velocity, amplitude, operation cycle, frequency, etc. along the X/Y/Z-axis of each joint in the three-dimensional space, so as to complete the quantitative evaluation of the basic AM.

### Recommended configuration

Server: Windows server 2016/2019 or CentOS 7.0 or Ubuntu Server 20.04 with Apache 2.0, PHP 7.4.3 and MySQL 8.0

Client: Windows 11/10/8/7/XP or MacOS or Ubuntu with Chrome or Firefox

### Login

Input the username and password to login AMA.

![Login](https://github.com/SHUTCM-tcme/AMA/blob/main/Screenshots/login.png "Login")

### Add new Participant

Click Add New Participant, then select the Participant type and gender, and input the Participant name, age and Practice Time in the pop-up page, click Submit to finish adding a new participant.

![Add a new Participant](https://github.com/SHUTCM-tcme/AMA/blob/main/Screenshots/addp.png "Add a new Participant")

### Add new record

Click Add new record corresponding to the newly added participant in the list page, and input the Folder name contain the uploaded data files of Simi Motion and select the Operation date, click submit to continue.

![Add a new record](https://github.com/SHUTCM-tcme/AMA/blob/main/Screenshots/addr.png "Add a new record")

### Analysis

Click Analysis corresponding to the newly added operation record, then select Skill and click Submit. The script will identify and display all the valid crests and troughs for manual review.

![Analysis](https://github.com/SHUTCM-tcme/AMA/blob/main/Screenshots/analysis.png "Analysis")

>>NOTE: A certain crest or trough can be reselected manually in the corresponding drop-down list if it is incorrectly identified by script.

### Get Results

Based on these crests and troughs, the average value of amplitudes and velocities along three axes of each tracking point, as well as the operating time of lifting, thrusting, twirling left, and twirling right actions can be calculated and displayed by the script.

![Get Results](https://github.com/SHUTCM-tcme/AMA/blob/main/Screenshots/report.png "Get Results")

### Contact information

Feel free to contact us if you have any question about this software.

Email: vincent.tang@shutcm.edu.cn (Wen-Chao Tang)
