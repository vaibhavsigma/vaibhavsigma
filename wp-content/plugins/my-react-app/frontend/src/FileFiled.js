import React, { useState } from 'react';

class FileFiled extends React.Component {
    constructor(props) {
        super(props);        
    }
    
    render() {
        const { onChangeEve } = this.props;
        return (
            <div className="form-group">
                <p>Upload Resume</p>
                <div className="input-group mb-3">
                    <input 
                        type="file"
                        name={this.props.FiledName? this.props.FiledName :''}
                        id={this.props.FiledId ? this.props.FiledId :''}
                        onChange={(e)=>onChangeEve(false, e)}
                        onFocus= {(e)=>onChangeEve(true, e)} // passing focus param true which focusout
                        className={this.props.eleClass?this.props.eleClass + " form-control":"form-control"}
                    />
                    <label className="input-group-text" htmlFor={this.props.FiledId ? this.props.FiledId :''  }>Upload</label>
                </div>
                <h6>{this.props.FiledValue}</h6>

                {this.props.ErrorMsg && (<small style={{color:"red",marginLeft:"10px"}}>{this.props.ErrorMsg}</small>) }
            </div>
        );
    }
}

export default FileFiled;