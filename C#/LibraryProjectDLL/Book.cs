using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Runtime.Serialization;
using System.Runtime.Serialization.Json;

//******************************************************
// File: Book.cs
//
// Purpose: Contains the class definition for Book
// the book class will hold all the books titles
// all the authors and all the prices
//
// Written By: Danny Gee
//
// Compiler: Visual Studio 2019
//
//****************************************************** 

namespace HW1DLL
{

    [DataContract]
    public class Book
    {

        #region MemberVar
        //initialization of variables for use
        private string title;
        private double price;

        //end of initialization of variables
        #endregion

        #region Properties
        //****************************************************
        // Method: Book
        //
        // Purpose: Default constructor: sets the default values of variables
        //**************************************************** 
        public Book()
        {
            title = "The Lion King";
            price = 19.99;
            authors = new List<Author>();
        }


        //****************************************************
        // Method: Title
        //
        // Purpose: get the value of title/set the value of title
        //**************************************************** 
        [DataMember(Name = "title")]
        public string Title
        {
            get
            {
                return title;
            }
            set
            {
                title = value;
            }
        }


        //**********************************************************
        // Method: Author
        //
        // Purpose: get the value of author/set the value of author
        //**********************************************************
        [DataMember(Name = "authors")]
        public List<Author> authors { get; set; }


        //****************************************************
        // Method: Title
        //
        // Purpose: get the value of title/set the value of title
        //**************************************************** 
        [DataMember(Name = "price")]
        public double Price
        {
            get
            {
                return price;
            }
            set
            {
                price = value;
            }
        }
        #endregion



        #region Methods
        //start of the override for the tostring function
        //****************************************************
        // Method: ToString
        //
        // Purpose: to override the ToString function with our print function
        //**************************************************** 
        public override string ToString()
        {
            string s;
            s = "The title of the book is: " + title + "\n" +
                    "The price of the book is: " + price + "\n" +
                    "The author(s) of the book is: \n";
            foreach (Author a in authors)
            {
                s += a.ToString();
            }
            return s;
        }
    }
    //******************************************************
    //End of the Book class
    //****************************************************** 
    #endregion
}
