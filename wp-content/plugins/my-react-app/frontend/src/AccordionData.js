import React, { useState } from 'react';

class AccordionData extends React.Component {
    constructor(props) {
        super(props);   
        this.state = {
            currentTabID : " "
        };     
        this.clickHandeler.bind(this)
    }

    clickHandeler(data){
        this.state.currentTabID == data ? this.setState({currentTabID: " "}) : this.setState({currentTabID: data});
    }
    
    render() {
        return (
            <div className="accordion" id="accordionExample">
                <div className="accordion-item">
                    <h2 className="accordion-header" id="headingOne" onClick={this.clickHandeler("headingOne")}>
                    <button className={"accordion-button" + (this.state.currentTabID == "headingOne"? "" :" collapsed")} type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Accordion Item #1
                    </button>
                    </h2>
                    <div id="collapseOne" className={"accordion-collapse collapse" + (this.state.currentTabID == "headingOne"? " show" :"")}  aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                    <div className="accordion-body">
                        <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                    </div>
                    </div>
                </div>
                <div className="accordion-item">
                    <h2 className="accordion-header" id="headingTwo" onClick={this.clickHandeler("headingTwo")}>
                    <button className={"accordion-button" + (this.state.currentTabID == "headingTwo"? "" :" collapsed")} type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Accordion Item #2
                    </button>
                    </h2>
                    <div id="collapseTwo" className={"accordion-collapse collapse" + (this.state.currentTabID == "headingTwo"? " show" :"")}  aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                    <div className="accordion-body">
                        <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                    </div>
                    </div>
                </div>
                <div className="accordion-item">
                    <h2 className="accordion-header" id="headingThree" onClick={this.clickHandeler("headingThree")}>
                    <button className={"accordion-button" + (this.state.currentTabID == "headingThree"? "" :" collapsed")} type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Accordion Item #3
                    </button>
                    </h2>
                    <div id="collapseThree" className={"accordion-collapse collapse" + (this.state.currentTabID == "headingThree"? " show" :"")} aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                    <div className="accordion-body">
                        <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                    </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default AccordionData;