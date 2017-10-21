# ZielonySklep

# HOW TO SETUP

Need:
  - docker
  - docker-compose
  
  `
  sudo apt-get install docker docker-compose
  `
  
## Step 1
  Setup the container some-mysql & some-prestashop.
  
  - Create Images of mysql and prestashop.
  
  `
  sudo docker-compose -f docker-compose.yaml build
  `
  
  - Run container from crated images of mysql and prestashop.
  
  `
  sudo docker-compose -f docker-compose.yaml up -d
  `
  
 ## Step 2
  Importing database
   
   - enter container with bash shell(look usefull command)
   - enter backupdbase folder
   - run export_database.sh
   - wait till the end
   
  # WARRNING
    DATABASE CONTAINS SITE CONTENT LIKE STYLES PRODUCTS CART CREDENTIALS
    REMEBER TO EXPORT DATABASE(import_database.sh) AND COMMIT CHANGES TO DBASE BRANCH!!!!!! 
   
 # USEFULL COMMAND
  
  
  - Show all containers.
  
  `
  sudo docker ps -a
  `
  
  - enter container <name||id> with bash shell.
  
  `
  sudo docker exec -it <name||id> bash
  example.
  sudo docker exec -it some-mysql bash
  `
  
  - Remove container <name||id>.
  
  `
  sudo docker rm -f <name||id>
  `
  
  - Remove image <imgName||imgId>.
  
  `
  sudo docker rmi <imgName||imgId>
  `
  
  - Start Stop Restart container <name||id>.
  
  `
  sudo docker start <name||id>
  sudo docker stop <name||id>
  sudo docker restart <name||id>
  `
