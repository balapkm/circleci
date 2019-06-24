def CMD

node {
    stage("checkout") {
        git url: 'https://github.com/balapkm/circleci.git'
        lastChanges format:'SIDE',matching: 'WORD', specificRevision: '1cb3c0080764f9c3acfb7a425aea85a97ecbf95e'
        //flywayrunner commandLineArgs: '', credentialsId: '29094ff4-b5ea-47ad-8491-6fdcd0756608', flywayCommand: 'migrate', installationName: 'Flyway', locations: "filesystem:$WORKSPACE/sql", url: 'jdbc:mysql://localhost:3306/test'
    }

    stage("last-changes") {
        def publisher = LastChanges.getLastChangesPublisher "PREVIOUS_REVISION", "SIDE", "LINE", true, true, "", "", "", "", ""
              publisher.publishLastChanges()
              def changes = publisher.getLastChanges()
              println(changes.getEscapedDiff())
              for (commit in changes.getCommits()) {
                  println(commit)
                  def commitInfo = commit.getCommitInfo()
                  println(commitInfo)
                  println(commitInfo.getCommitMessage())
                  println(commit.getChanges())
              }
        /*def changeLogSets = currentBuild.changeSets
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
        }*/
    }

    /*stage("Approval") {
        /*emailext (
            subject: "Job '${env.JOB_NAME} ${env.BUILD_NUMBER}'",
            body: """Kindly Approve this process -> ${env.BUILD_URL}/input/""",
            to: "balakumaran.g@infinitisoftware.net",
            from: "balakumaran.raji@gmail.com"
        )
        timeout(time: 1, unit: 'HOURS') {
            //input 'Deploy to Production?'
            input message: 'Kindly review current version code  and approve for movement of development server?', ok: 'Approve', parameters: [string(defaultValue: '', description: '', name: 'Reject Reason', trim: false)], submitter: 'balakumaran'
        }
    }

    stage("Move to server") {
        println "$CMD"
        /*if(CMD != ""){
            sshagent(credentials : ['Balakumaran']) {
                sh "$CMD"
            }
        }
    }*/
}
