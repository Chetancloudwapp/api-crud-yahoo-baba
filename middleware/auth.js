const jwt = require('jsonwebtoken');
const User = require('../models/users.model');

// create middleware function
const auth = async(req, res, next) => {
    try{
        const bearerHeader = req.headers['authorization']; // token header mai authorization ki key mai aayga
        if(typeof bearerHeader !== 'undefined'){
            const token = bearerHeader.split(' ')[1]; // token ko split karke dusre index se uthana hai because hume without bearer token chahiye
            // verify token
            const user = jwt.verify(token, process.env.JWT_SECRET);

            console.log(user);
            req.token = user
            next(); // next middleware ko call krna hai
        }else{
            res.status(401).json({ message : 'Unauthorized access!'});
        }
    }catch(error){
        console.log(error);
        return res.status(403).json({ message: 'Invalid or expired token!'});
    }
    
}

module.exports = auth;