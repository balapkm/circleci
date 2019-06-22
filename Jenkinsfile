def CMD

pipeline {
    agent any
    stages{
        stage("checkout") {
            steps{
                git url: 'https://github.com/balapkm/circleci.git'
            }

            steps {
                def changeLogSets = currentBuild.changeSets
                for (int i = 0; i < changeLogSets.size(); i++) {
                    def entries = changeLogSets[i].items
                    for (int j = 0; j < entries.length; j++) {
                        def entry = entries[j]
                        def files = new ArrayList(entry.affectedFiles)
                        CMD = ""
                        for (int k = 0; k < files.size(); k++) {
                            def file = files[k]
                            def dest_dir = "/var/www/html/circleci";
                            if(CMD != ""){
                                CMD = "$CMD && scp  -o StrictHostKeyChecking=no $WORKSPACE/$file.path ubuntu@ec2-13-232-76-112.ap-south-1.compute.amazonaws.com:$dest_dir/$file.path"
                            }else{
                                CMD = "scp  -o StrictHostKeyChecking=no $WORKSPACE/$file.path ubuntu@ec2-13-232-76-112.ap-south-1.compute.amazonaws.com:$dest_dir/$file.path"
                            }
                        }
                  }
                }
            }
        }
        stage('Example') {
            input {
                message "Should we continue?"
                ok "Yes, we should."
                submitter "alice,bob"
                parameters {
                    string(name: 'PERSON', defaultValue: 'Mr Jenkins', description: 'Who should I say hello to?')
                }
            }
            steps {
                echo "Hello, ${PERSON}, nice to meet you."
            }
        }
    }
}
