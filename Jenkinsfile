def CMD

node {
    stage("checkout") {
        git url: 'https://github.com/balapkm/circleci.git'
        flywayrunner commandLineArgs: '', credentialsId: '29094ff4-b5ea-47ad-8491-6fdcd0756608', flywayCommand: 'migrate', installationName: 'Flyway', locations: "filesystem:$WORKSPACE/sql", url: 'jdbc:mysql://localhost:3306/test'
    }

    stage("last-changes") {
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
                    println "$file.path"
                    if(CMD != ""){
                        CMD = "$CMD && scp  -o StrictHostKeyChecking=no $WORKSPACE/$file.path ubuntu@ec2-13-232-76-112.ap-south-1.compute.amazonaws.com:$dest_dir/$file.path"
                    }else{
                        CMD = "scp  -o StrictHostKeyChecking=no $WORKSPACE/$file.path ubuntu@ec2-13-232-76-112.ap-south-1.compute.amazonaws.com:$dest_dir/$file.path"
                    }
                }
          }
        }
    }

    stage("Approval") {
        /*emailext (
            subject: "Job '${env.JOB_NAME} ${env.BUILD_NUMBER}'",
            body: """Kindly Approve this process -> ${env.BUILD_URL}/input/""",
            to: "balakumaran.g@infinitisoftware.net",
            from: "balakumaran.raji@gmail.com"
        )*/
        timeout(time: 1, unit: 'HOURS') {
            input 'Deploy to Production?'
        }
    }

    stage("Move to server") {
        println "$CMD"
        /*if(CMD != ""){
            sshagent(credentials : ['Balakumaran']) {
                sh "$CMD"
            }
        }*/
    }
}
