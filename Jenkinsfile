pipeline {
    agent any
    environment {
        DOCKER_TAG = ''
    }

    stages {
        stage('SCM') { // Checkout repository first
            steps {
                script {
                    try {
                        git credentialsId: 'kopikita', 
                            url: 'https://github.com/badfellas/KopiKita'
                    } catch (Exception e) {
                        error "SCM checkout failed: ${e.message}"
                    }
                }
            }
        }

        stage('Set Version') { // Moved after SCM
            steps {
                script {
                    try {
                        def commitHash = bat(script: 'git rev-parse --short HEAD', returnStdout: true).trim()
                        env.DOCKER_TAG = commitHash
                    } catch (Exception e) {
                        error "Failed to get commit hash: ${e.message}"
                    }
                }
            }
        }

        stage('Docker Build') {
            when {
                expression { currentBuild.result == null }
            }
            steps {
                script {
                    try {
                        bat "docker build -t final-supermarket-web-main:latest ."
                    } catch (Exception e) {
                        error "Docker build failed: ${e.message}"
                    }
                }
            }
        }
    }

    post {
        always {
            echo 'Cleaning up workspace...'
            cleanWs()
        }
        success {
            echo 'Pipeline completed successfully.'
            discordSend description: "✅ Build berhasil!!! Image Docker berhasil dibuat dengan tag: ${env.DOCKER_TAG}. 🚀 Cek log lengkap di Jenkins.", 
                        footer: 'Jenkins CI/CD - Build Sukses', 
                        webhookURL: 'https://discord.com/api/webhooks/1321809292579311628/Dro67YopANf12JD2EyTTt3rFKac2sA56gHNicZY2xOyRczZJaA68uTyJB9MQx9wwtqds'
        }

        failure {
            echo 'Pipeline failed. Check logs for details.'
            // Kirim notifikasi ke Discord jika build gagal
            discordSend description: '❌ Build gagal. Silakan cek detail error di Jenkins untuk penyebab kegagalan. ⚠️', 
                        footer: 'Jenkins CI/CD - Build Gagal', 
                        webhookURL: 'https://discord.com/api/webhooks/1321809292579311628/Dro67YopANf12JD2EyTTt3rFKac2sA56gHNicZY2xOyRczZJaA68uTyJB9MQx9wwtqds'
        }
    }
}
