import React, { useState } from 'react';

class CheckBoxFiled extends React.Component {
    constructor(props) {
        super(props);        
    }
    
    render() {
        const { onChangeEve } = this.props;
        return (
            <div className="form-group">
                <p className={this.props.FiledValue.join(",")}>{this.props.name}</p>
                {this.props.options.map((value, index) => (
                    <div className="form-check " key={this.props.name.replace(" ","-") + value.replaceAll(" ","-") + index}>
                        <input 
                            className="form-check-input" 
                            type="checkbox" 
                            name={this.props.name} 
                            value={value} 
                            defaultChecked={this.props.FiledValue.includes(value)} 
                            id={this.props.name.replace(" ","-") + value.replaceAll(" ","-") + index} 
                            onChange={(e)=>onChangeEve(true, e)} 
                            />
                        <label 
                            className="form-check-label" 
                            htmlFor={this.props.name.replace(" ","-") + value.replaceAll(" ","-") + index} 
                            >
                            { value }
                        </label>
                    </div>
                ))}
                {this.props.ErrorMsg && (<small style={{color:"red",marginLeft:"10px"}}>{this.props.ErrorMsg}</small>) }
            </div>
        );
    }
}

export default CheckBoxFiled;