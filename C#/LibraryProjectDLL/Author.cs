using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Runtime.Serialization;
using System.Runtime.Serialization.Json;
using System.IO;


//******************************************************
// File: authorAndBook.cs
//
// Purpose:This file contains all the necessary information for defining an author including
// First name, Last name, and their background
//
// Written By: Danny Gee
//
// Compiler: Visual Studio 2019
//
//****************************************************** 
namespace HW1DLL
{
    [DataContract]
    public class Author
    {
        #region MemberVariables
        //initializes all the variables for use
        private string fName;
        private string lName;
        private string bGround;
        //end of initialization of variables for use
        #endregion

        #region Properties
        //****************************************************
        // Method: Author
        //
        // Purpose: to initialize default variables
        //**************************************************** 
        public Author()
        {
            fName = "Danny";
            lName = "Gee";
            bGround = "An aspiring author and programmer coming from the small town of babylon.";
        }
        //end of default constructor

        //start of the C# properties of the getter and setter using JSON
        //getter and setter for first name
        //****************************************************
        // Method: First
        //
        // Purpose: to get the value or set the value of First
        //**************************************************** 
        [DataMember(Name = "first")]
        public string First
        {
            get
            {
                return fName;
            }
            set
            {
                fName = value;
            }
        }
        //****************************************************
        // Method: Last
        //
        // Purpose: to get the value or set the value of Last
        //**************************************************** 
        //getter and setter for last name
        [DataMember(Name = "last")]
        public string Last
        {
            get
            {
                return lName;
            }
            set
            {
                lName = value;
            }
        }
        //****************************************************
        // Method: Background
        //
        // Purpose: to get the value or set the value of Last
        //**************************************************** 
        //getter and setter for background information
        [DataMember(Name = "background")]
        public string Background
        {
            get
            {
                return bGround;
            }
            set
            {
                bGround = value;
            }
        }
        //End of the C# properties of the getter and setter functions using JSON

        #endregion
        #region Methods
        //****************************************************
        // Method: ToString
        //
        // Purpose: to override the ToString function with our print function
        //**************************************************** 

        //start of the override for the tostring function
        public override string ToString()
        {
            string s;
            s = "The first name of the author is: " + fName + "\n" +
                "The last name of the author is: " + lName + "\n" +
                "The Author's background is: " + bGround + "\n";
            return s;
        }
        #endregion
    }
}



