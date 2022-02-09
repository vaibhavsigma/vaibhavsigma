import React, { useState } from 'react';
import InputFiled from './InputFiled';
import TextareaFiled from './TextareaFiled';
import RadioFiled from './RadioFiled';
import CheckBoxFiled from './CheckBoxFiled';
import SelectFiled from './SelectFiled';
import FileFiled from './FileFiled';
import RangeFiled from './RangeFiled';
import AccordionData from './AccordionData';
import { Navigate } from "react-router-dom";


const regExp = RegExp(
    /^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[A-Za-z]+$/
)

class DemoForm extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            emailId: "",
            emailError: "",
            password: "",
            passwordError: "",
            successMsg:"",
            message:"",
            messageError:"",
            radioName: this.props.radio && this.props.radio[0] ? this.props.radio[0] : "",
            radio:"",
            radioError:"",
            checkboxName: this.props.checkbox && this.props.checkbox[0] ? this.props.checkbox[0] : "",
            checkbox:[],
            checkboxError:"",
            selectName: this.props.select && this.props.select[0] ? this.props.select[0] : "",
            selectIgnoreFirst: this.props.select && this.props.select[2] ? this.props.select[1][0] : false,
            select:"",
            selectError:"",
            file:"",
            fileError:"",
            fileName:"",
            percentage:45,
            percentageError:"",
            HobbyVal:"",
            redirect:null,
        };
        // this.formEleFocus = this.formEleFocus.bind(this);
        // this.formEleFocusout = this.formEleFocusout.bind(this);
        // this.formValChange = this.formValChange.bind(this);
      }

    formValChange( focus, e ){
        // e.preventDefault();
        const { name, value } = e.target;
        let errorMsg = "";
        // console.log(name, value, this.state.select );
        this.state.successMsg= "";
        switch (name) {
            case "email":
                if(focus ){
                    if(value){
                        this.state.emailError = regExp.test(value) ? "" : "Email address is invalid";
                    } else{
                        this.state.emailError = "This filed required.";
                    }
                } else {
                    this.state.emailError = "";
                }
                this.setState({
                    emailId : value, 
                });
                break;
            case "password":
                if( focus ){
                    if(value){
                        errorMsg = value.length < 6 ? "Atleast 6 characaters required" : "";
                        errorMsg = value.length > 12 ? "characaters limit is 12" : errorMsg;                    
                    } else {
                        errorMsg = "This filed required.";
                    }
                    this.state.passwordError = errorMsg;
                } else {
                    this.state.passwordError = "";
                }
                this.setState({
                    password: value, 
                });
                break;
            case "percentage":
                this.setState({
                    percentage: value, 
                });
                break;            
            case "resume":
                if(e.target.files[0] && e.target.files[0].name){
                    this.state.fileError = "";
                    this.state.fileName = e.target.files[0].name;
                } else {
                    if( focus ){ 
                        this.state.fileError = "";
                    } else{
                        this.state.fileError = "This filed required.";
                    }
                    this.state.fileName = "";
                }
                this.setState({
                    file: e.target.files[0], 
                });
                break;               
            case "message":
                if(focus ){
                    if(value){
                        this.state.messageError = value.length > 120 ? "characaters limit is 120" : errorMsg;                    
                    } else {
                        this.state.messageError = "This filed required.";
                    }
                } else {
                    this.state.messageError = "";
                }
                this.setState({
                    message: value, 
                });
                break;
            case this.state.radioName:
                if(this.state.radioError ){
                    if(value){
                        this.state.radioError = "";
                    }
                }
                this.setState({
                    radio: value, 
                });
                break;
            case this.state.checkboxName:
                if(e.target.checked ){
                    if( !this.state.checkbox.includes(value) ){
                        this.state.checkbox.push(value)
                    }                        
                } else {
                    if( this.state.checkbox.includes(value) ){
                        this.state.checkbox =  
                            this.state.checkbox.length > 1 ? 
                                this.state.checkbox.filter(item => item !== value) :
                                [];
                    }  
                }
                if(this.state.checkbox.length > 0){
                    this.state.checkboxError = "";
                } else {
                    this.state.checkboxError = "Please select any option from above.";
                }
                this.forceUpdate();
                break;
            case this.state.selectName:
                if(value && this.state.selectIgnoreFirst != value){
                    this.state.selectError = "";
                } else {
                    this.state.selectError = "This filed required.";
                }
                this.setState({
                    select: value, 
                });
                break;                
            default:
                break;
        }
    }
    
    onSubmit = async( e ) => {
        e.preventDefault();
        this.state.successMsg = "";
        let checkError = true;
        if( !this.state.emailId ){
            checkError = false;
            this.setState({
                emailError: "This filed required.", 
            });
        }

        if( !this.state.password ){
            checkError = false;
            this.setState({
                passwordError: "This filed required.", 
            });
        }

        if( !this.state.message ){
            checkError = false;
            this.setState({
                messageError: "This filed required.", 
            });
        }

        if( !this.state.file ){
            checkError = false;
            this.setState({
                fileError: "This filed required.", 
            });
        }

        if( this.state.radioName && !this.state.radio ){
            checkError = false;
            this.setState({
                radioError: "Please select any one option from above.", 
            });
        }

        if( this.state.checkboxName && !this.state.checkbox.length > 0 ){
            checkError = false;
            this.setState({
                checkboxError: "Please select any option from above.", 
            });
        }

        if( this.state.selectName && ( !this.state.select || this.state.selectIgnoreFirst == this.state.select) ){
            checkError = false;
            this.setState({
                selectError: "This filed required.", 
            });
        }

        if( checkError && !this.state.emailError && !this.state.passwordError ){
            try {
                localStorage.setItem("formSubmit", JSON.stringify({
                        email: this.state.emailId,
                        password: this.state.password,
                        percentage: this.state.percentage,
                        Hobby: Object.assign({}, this.state.checkbox),
                        Gender: this.state.radio,
                        resume: this.state.fileName,
                        Age: this.state.select,
                        message: this.state.message,
                    })
                );
                // e.target.submit();
                this.setState({
                    redirect:true,
                });
            } catch (err) {
                this.setState({
                    successMsg:"Error, ocuured while Submiting form!",
                });
                console.log(err);
            }
        }
        
    };    

    render(){
        return (

            <div className="container">
                <div className="row">
                    <div className="col-sm-12 col-md-4"></div>
                    <div className="col-sm-12 col-md-4">
                        <form id="myForm" onSubmit={this.onSubmit} action="/new-starter/offers" method="post" >
                            <input type="hidden" name="Hobby" value={this.state.HobbyVal} />
                            <InputFiled 
                                FiledName="email"
                                FiledId="your-email"
                                FiledValue={this.state.emailId}
                                ErrorMsg={this.state.emailError}
                                onChangeEve={this.formValChange.bind(this)}
                                FIledLabel="Email"
                            />
                            <br/>
                            <InputFiled 
                                FiledType="password"
                                FiledName="password"
                                FiledId="your-password"
                                FIledLabel="Password"
                                FiledValue={this.state.password}
                                ErrorMsg={this.state.passwordError}
                                onChangeEve={this.formValChange.bind(this)}
                            /> 
                            <br/>
                            <RangeFiled 
                                FiledName="percentage"
                                FiledId="your-percentage"
                                FIledLabel="Select Your Percentage"
                                FiledValue={this.state.percentage}
                                Min="45"
                                Max="100"
                                ErrorMsg={this.state.percentageError}
                                onChangeEve={this.formValChange.bind(this)}
                                ValPostLabel=" %"
                            />
                            <br/>
                            <FileFiled 
                                FiledName="resume"
                                FiledId="your-resume"
                                FIledLabel="Upload"
                                FiledValue={this.state.fileName}
                                ErrorMsg={this.state.fileError}
                                onChangeEve={this.formValChange.bind(this)}
                            /> 
                            <br/>
                            {this.props.radio && this.props.radio.length == 2 ?
                                <RadioFiled 
                                    name={this.props.radio[0]} 
                                    options={this.props.radio[1]}
                                    FiledValue={this.state.radio}
                                    ErrorMsg={this.state.radioError}
                                    onChangeEve={this.formValChange.bind(this)}
                                /> : 
                                null }
                            <br/>
                            {this.props.checkbox && this.props.checkbox.length > 1 ?
                                <CheckBoxFiled 
                                    name={this.props.checkbox[0]} 
                                    options={this.props.checkbox[1]}
                                    FiledValue={this.state.checkbox}
                                    ErrorMsg={this.state.checkboxError}
                                    onChangeEve={this.formValChange.bind(this)}
                                /> : 
                                null }
                            <br/>
                            {this.props.select && this.props.select.length > 1 ?
                                <SelectFiled     
                                    name={this.props.select[0]} 
                                    options={this.props.select[1]}
                                    FiledValue={this.state.select}
                                    ErrorMsg={this.state.selectError}
                                    onChangeEve={this.formValChange.bind(this)}
                                /> : 
                                null }
                            <br/>
                            <TextareaFiled 
                                FiledName="message"
                                FiledId="your-message"
                                FIledLabel="Message"
                                FiledValue={this.state.message}
                                ErrorMsg={this.state.messageError}
                                onChangeEve={this.formValChange.bind(this)}
                            />
                            <br/>
                            {/* <AccordionData /> */}
                            <br/>
                            <InputFiled 
                                FiledType="Submit"
                                FiledName="submit-form"
                                FiledValue="Submit"
                                eleClass="btn btn-primary mb-2"
                                onChangeEve={this.formValChange.bind(this)}
                            />
                            <br/>
                            {this.state.successMsg && ( 
                                <div className="alert alert-success" role="alert">
                                    {this.state.successMsg}
                                </div>) }
                                                        
                        </form>     
                        <div id="message-box"></div>               
                        <div id="message-box2"></div>
                    </div>
                </div>
                {this.state.redirect? <Navigate to="/new-starter/offers" /> : null }
            </div>
        );
    }    
}

export default DemoForm;

/*
    static getDerivedStateFromProps(props, state) {
        console.log(props);
        setTimeout(() => {
            this.setState({passwordError: "Please fill above filed to contunue."})
          }, 1000)
      
        return {passwordErrorname: "Please fill above filed to contunue."};
        return{}
    }

    componentDidMount() {
        setTimeout(() => {
            this.setState({passwordError: "Please fill above filed to contunue."})
            console.log("hello", this.state.passwordError);
          }, 1000)
    }
    
    getSnapshotBeforeUpdate(prevProps, prevState) {
        document.getElementById("message-box").innerHTML =
        "Before the update, the Email was: " + prevState.emailId;
        return{}
    }
    
    componentDidUpdate() {
        document.getElementById("message-box2").innerHTML =
        "Before the update, the Email was: " + this.state.emailId;
    }
*/