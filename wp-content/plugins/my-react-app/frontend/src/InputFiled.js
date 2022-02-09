import React, { useState } from 'react';

class InputFiled extends React.Component {
    constructor(props) {
        super(props);        
    }
    
    render() {
        const { onChangeEve } = this.props;
        return (
            <div className="form-group">
                {this.props.FIledLabel && (<label htmlFor={this.props.FiledId ? this.props.FiledId :''  }>{this.props.FIledLabel}</label>) }
                <input 
                    onBlur= {(e)=>onChangeEve(true, e)} // passing focus param true which focusout
                    onFocus= {(e)=>onChangeEve(false, e)} // passing focus param true which focusout
                    type={this.props.FiledType? this.props.FiledType :'text'}
                    name={this.props.FiledName? this.props.FiledName :''}
                    id={this.props.FiledId ? this.props.FiledId :''}
                    value={this.props.FiledValue?this.props.FiledValue:''}
                    onChange={(e)=>onChangeEve(false, e)}
                    className={this.props.eleClass?this.props.eleClass + " form-control":"form-control"}
                />
                {this.props.ErrorMsg && (<small style={{color:"red",marginLeft:"10px"}}>{this.props.ErrorMsg}</small>) }
            </div>
        );
    }
}

export default InputFiled;