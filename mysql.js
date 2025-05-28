var mysql = require('mysql');
var con = mysql.createConnection({
    host: "localhost",
    user: "yourusername",
    password: "yourpassword",
    database: "nombre_de_la_base_de_datos"
});

con.connect(function(err){
    if(err) throw err;
    console.log("Connected!");
});

