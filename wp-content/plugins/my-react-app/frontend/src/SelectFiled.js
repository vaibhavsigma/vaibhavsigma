import React, { useState } from 'react';

class SelectFiled extends React.Component {
    constructor(props) {
        super(props);        
    }
    
    render() {
        const { onChangeEve } = this.props;
        return (
            <div className="form-group">
                <p>{this.props.name}</p>
                <select 
                    className="form-select" 
                    onChange={(e)=>onChangeEve(false, e)}
                    onBlur= {(e)=>onChangeEve(true, e)} // passing focus param true which focusout
                    onFocus= {(e)=>onChangeEve(false, e)} // passing focus param true which focusout
                    name={this.props.name}
                    defaultValue={this.props.FiledValue}
                    >
                    {this.props.options.map((value, index) => (
                        <option 
                            value={value} 
                            key={value.replaceAll(" ","-")}
                            >
                            {value}
                        </option>
                    ))}
                </select>

                {this.props.ErrorMsg && (<small style={{color:"red",marginLeft:"10px"}}>{this.props.ErrorMsg}</small>) }
            </div>
        );
    }
}

export default SelectFiled;