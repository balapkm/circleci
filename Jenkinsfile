def CMD

node {
    stage("checkout") {
        git url: 'https://github.com/balapkm/circleci.git'
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
                    if(CMD != ""){
                        CMD = "$CMD && scp  -o StrictHostKeyChecking=no $WORKSPACE/$file.path ubuntu@ec2-13-232-76-112.ap-south-1.compute.amazonaws.com:$dest_dir/$file.path"
                    }else{
                        CMD = "scp  -o StrictHostKeyChecking=no $WORKSPACE/$file.path ubuntu@ec2-13-232-76-112.ap-south-1.compute.amazonaws.com:$dest_dir/$file.path"
                    }
                }
          }
        }
    }

    stage("check-changes") {
        println "$CMD"
        if(CMD != ""){
            sshagent(credentials : ['Balakumaran']) {
                sh "$CMD"
            }
        }
    }
}
