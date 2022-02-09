import React, { useState } from 'react';

class RangeFiled extends React.Component {
    constructor(props) {
        super(props);      
        this.showGrade = this.showGrade.bind(this);
    }
    showGrade(value){
        if(value > 85)
            return "A";
        if(value > 75)
            return "B";
        if(value > 60)
            return "C";
        
        return "D";
    }
    render() {
        const { onChangeEve } = this.props;
        return (
            <div className="form-group">
                {this.props.FIledLabel && (<label htmlFor={this.props.FiledId ? this.props.FiledId :''  }>{this.props.FIledLabel}</label>) }
                <div className="input-group mb-3">
                    <input 
                        type="range"
                        onBlur= {(e)=>onChangeEve(true, e)} // passing focus param true which focusout
                        name={this.props.FiledName? this.props.FiledName :''}
                        id={this.props.FiledId ? this.props.FiledId :''}
                        value={this.props.FiledValue?this.props.FiledValue:''}
                        onChange={(e)=>onChangeEve(true, e)}
                        className={this.props.eleClass?this.props.eleClass + " form-range":"form-range"}
                        step={this.props.Step?this.props.Step:"1"}
                        min={this.props.Min?this.props.Min:"0"}
                        max={this.props.Max?this.props.Max:"100"}
                    />
                </div>
                <h6> Selected Value: {this.props.FiledValue}{this.props.ValPostLabel} Grade( {this.showGrade(this.props.FiledValue)})</h6>
                {this.props.ErrorMsg && (<small style={{color:"red",marginLeft:"10px"}}>{this.props.ErrorMsg}</small>) }
            </div>
        );
    }
}

export default RangeFiled;