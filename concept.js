/*

API DEVELOPMENT :- It provide an interface between frontend or android device with the database.

Read   :- app.get('/profile', (req, res) => {})
create :- app.post('/profile', (req, res) => {})
update :- app.put('/profile', (req, res) => {})
delete :- app.delete('/profile', (req, res) => {})

Note:- Api mai data ko show krne ke liye humare pass response ka ek method aata hai res.json()


// ---------------------- How to use API in Frontend ---------------------- //

1). Javascript Fetch Method
2). Jquery $.Ajax() method
3). ReactJs
4). AngularJs
5). VueJs
6). Axiom

// ------------------------ CORS Package --------------------- //

Note :- Now let suppose humare backend ki api www.test.com pr bani hui hai and hum chahte hai ki humara jo frontend hai vo xyz.com pr hai to humare backend ki api ko xyz.com se access karne ke liye hume cors package ki jarurat hoti hai.

CORS :- Cross Origin Resource Sharing
CORS is a mechanism that allows restricted resources on a web page to be requested from another domain outside the domain from which the first resource was served.
// CORS is a security feature implemented by web browsers to prevent malicious websites from making requests to a different domain than the one that served the original web page.
=> Now let suppose humare pass multiple servers hai and hum chahte hai ki koi specific server hi humare backend ki api ko access kar sake to uske liye hum cors package ka use karte hai.

************** Steps to working with CORS **************

1). Install CORS package
2). const cors = require('cors');
3). app.use(cors()); // use cors as a middleware

***************** How to enable cors for a single route ********

app.get('/profile', cors(), (req, res) => {}); // only isi route ka access hai dusre server ko


******************** How to allow access to specific Origin ***************

Note:- Isme sare parameters optionals hote hai

const corsOptions = {
  origin: 'http://example.com', // kis origin ko allow krna chahte hai hum
  methods: ['GET', 'POST'], // Allowed HTTP methods
  allowedHeaders: ['Content-Type', 'Authorization'], // only authorize user hi access krskta haii
  optionsSuccessStatus: 200 // here 200 is the default status code
};

app.use(cors(corsOptions)); // use cors as a middleware


************************ Or we can also write like this ***********************

const corsOptions = {
    origin: 'http://example.com',
    origin: ['http://example.com','http://example1.com','http://example2.com'], // for allowing multiple origins
    credentials: true, // Allow credentials (cookies, authorization headers, etc.)
}

*/