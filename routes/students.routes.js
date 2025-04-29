const express = require('express');
const router = express.Router();
const multer = require('multer');
const path = require('path');
const fs = require('fs') // here fs is file system
const Student = require('../models/students.model') // import the model file here 

// define storage
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
       cb(null, './uploads')
    },
    filename:(req, file, cb) => {
        const newFileName = Date.now() + path.extname(file.originalname)
        cb(null, newFileName)
    }
})

// file filter
const fileFilter = (req, file, cb) => {
    if(file.mimetype.startsWith('image/')){
        cb(null, true)
    }else{
        cb(new Error("Only images are allowed", false));
    }
}

// image upload
const upload  = multer({
    storage: storage,
    fileFilter: fileFilter,
    limit: {
        fileSize : 1024 * 1024 * 3
    }
})

// Get all students
router.get('/', async(req, res) => {
    try{
        const students = await Student.find();
        res.json(students);
    }catch(err){
        res.status(500).json({message:err.message});
    }
})

// Get a single student
router.get('/:id', async(req, res) => {
    try{
        const students = await Student.findById(req.params.id);
        if(!students){
            return res.status(404).json({message:'Student not found!'});
        }
        res.json(students);
    }catch(err){
        res.status(500).json({message:err.message});
    }
})


// Add new student
router.post('/', upload.single('profile_pic'), async(req, res) => {
    try{
        // const newStudent = await Student.create(req.body);

        const student = new Student(req.body);
        if(req.file){
            student.profile_pic = req.file.filename
        }

        const newStudent = await student.save()
        res.status(201).json(newStudent);
    }catch(err){
        res.status(400).json({message:err.message});
    }
});

// update a student
router.put('/:id', upload.single('profile_pic'), async(req, res) => {
    try{
        const existingStudent = await Student.findById(req.params.id);
        if(!existingStudent){
            if(req.file.filename){
                const filePath = path.join('./uploads', req.file.filename);
                fs.unlink(filePath, (err) => {
                    if(err) {
                        console.log('Failed to delete image: ', err);
                    }
                })
            }

            return res.status(404).json({message:'Student not found!'});
        } 

        if(req.file) {
            if(existingStudent.profile_pic) {
                const oldImagePath = path.join('./uploads', existingStudent.profile_pic);
                fs.unlink(oldImagePath, (err) => {
                    if(err) {
                        console.log('Failed to delete old image: ', err);
                    }
                })
            }

            req.body.profile_pic = req.file.filename
        }

        const updatedStudent = await Student.findByIdAndUpdate(req.params.id, req.body, {new:true}); // here new specifies ki only new data hi return hoga
        if(!updatedStudent) return res.status(404).json({message:'Student not found!'});

        res.json(updatedStudent);
    }catch(err){
        res.status(400).json({message:err.message});
    }
});

// Delete a student
router.delete('/:id', async(req, res) => {
    try{
        const student = await Student.findByIdAndDelete(req.params.id);
        if(!student) return res.status(404).json({message:'Student not found!'});

        // delete existing image
        if(student.profile_pic){
            const filePath = path.join('./uploads', student.profile_pic);
            fs.unlink(filePath, (err) => {
                if(err) {
                    console.log('Failed to delete: ', err);
                }
            })
        }

        res.json({message:'Student deleted successfully!'});
    }catch(err){
        res.status(500).json({message:err.message});
    }
});

module.exports = router

// Note:- Filesystem (fs) ka use server se file ko delete krne, update krne ya create krne k liye hota hai