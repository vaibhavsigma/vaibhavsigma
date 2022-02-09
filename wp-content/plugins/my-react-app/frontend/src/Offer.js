import { Link } from 'react-router-dom'
const Offer = () => {
    let $formdata = localStorage.getItem("formSubmit")?  JSON.parse(localStorage.getItem("formSubmit")):false;
    let Hobby = [];
    for(var i in $formdata.Hobby)
        Hobby.push($formdata.Hobby[i]);

    return (
        <div className="container">
            <div className="row">
                <div className="col-sm-12 col-md-2"></div>
                <div className="col-sm-12 col-md-6">  
                    <h2 style={{textAlign:"center"}}>Your Details</h2>
                    { $formdata ?
                        (<div>
                            <p><strong>Email:</strong> {$formdata && $formdata.email ? $formdata.email : " - "}</p>
                            <p><strong>Percentage:</strong> {$formdata && $formdata.percentage ? $formdata.percentage : " - "}</p>
                            <p><strong>Resume(File Name):</strong> {$formdata && $formdata.resume ? $formdata.resume : " - "}</p>
                            <p><strong>Gender:</strong> {$formdata && $formdata.Gender ? $formdata.Gender : " - "}</p>
                            <p><strong>Hobby:</strong> {Hobby ? Hobby.join():" - "}</p>
                            <p><strong>Age:</strong> {$formdata && $formdata.Age && $formdata.Age ? $formdata.Age : " - "}</p>
                            <p><strong>Message:</strong> {$formdata && $formdata.message ? $formdata.message : " - "}</p>
                        </div>) : 
                        <h5>Something went wrong, No Details are Found!</h5>
                    }
                    <Link to="/new-starter/react-app-test">Go Back</Link>
                </div>
            </div>
        </div>);
  };
  
  export default Offer;
  