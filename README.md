## Instruções de Execução (Docker)
O projeto está configurado para ser executado via Docker.

Na raiz do projeto (onde está o ficheiro `Dockerfile`), abra o terminal e execute: 
`docker build -t gestao-dosimetro .`

Depois da alínea anterior executada, execute: 
`docker run -p 8080:80 --name gestao-dosimetro gestao-dosimetro`

Depois aceda ao link http://localhost:8080 

## Recomendação
Depois de testar, se quiser que os dados na base voltem como estavam no início, execute o ficheiro `inicializar.sql`.

## Contas
Para testar as diferentes funcionalidades e perfis de utilizador, utilize as seguintes contas pré-configuradas:

Administradores:
-> email: ana@mail.com; password: pass123
-> email: ricardo@mail.com; password: pass123

Físico Médico:
-> email: joao@mail.com; password: pass123
-> email: mariana@mail.com; password: pass123
-> email: pedro@mail.com; password: pass123
-> email: duarte@mail.com; password: pass123

Profissionais de Saúde:
-> email: carlos@mail.com; password: pass123
-> email: ines@mail.com; password: pass123
-> email: paulo@mail.com; password: pass123
-> email: sofia@mail.com; password: pass123
-> email: bruno@mail.com; password: pass123
-> email: lara@mail.com; password: pass123
-> email: rui@mail.com; password: pass123
-> email: marta@mail.com; password: pass123 (inativa)
-> email: tiago@mail.com; password: pass123
-> email: vera@mail.com; password: pass123
-> email: elisa@mail.com; password: pass123
-> email: fabio@mail.com; password: pass123
-> email: gustavo@mail.com; password: pass123