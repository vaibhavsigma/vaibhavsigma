import React, { useState } from 'react';

class RadioFiled extends React.Component {
    constructor(props) {
        super(props);        
    }
    
    render() {
        const { onChangeEve } = this.props;
        return (
            <div className="form-group">
                <p>{this.props.name}</p>
                {this.props.options.map((value, index) => (
                    <div className="form-check" key={this.props.name.replace(" ","-") + value.replaceAll(" ","-") + index}>
                        <input 
                            className="form-check-input" 
                            type="radio" 
                            name={this.props.name} 
                            value={value} 
                            defaultChecked={this.props.FiledValue === value} 
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

export default RadioFiled;