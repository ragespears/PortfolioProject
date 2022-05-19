using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Linq;
using System.Runtime.Serialization;
using System.Text;
using System.Threading.Tasks;

//******************************************************
// File: GameInfo.cs
//
// Purpose: Contains the class definition for publisher
// the publisher will find all books related to the publisher
//
// Written By: Danny Gee
//
// Compiler: Visual Studio 2019
//
//****************************************************** 

namespace HW1DLL
{
    [DataContract]
    public class publisher
    {

        #region properties

        //****************************************************
        // Method: Books
        //
        // Purpose: get the values for books/ set the values for books
        //**************************************************** 
        [DataMember(Name = "books")]
        public ObservableCollection<Book> books { get; set; }



        //****************************************************
        // Method: Name
        //
        // Purpose: get the values for Name/ set the values for Name
        //****************************************************

        [DataMember(Name = "name")]
        public string Name { get; set; }
        #endregion

        #region methods
        //****************************************************
        // Method: Publisher
        //
        // Purpose: sets the default values for member variables
        //**************************************************** 
        public publisher()
        {
            Name = "Roosevelt Books inc.";
            books = new ObservableCollection<Book>();
        }
        //****************************************************
        // Method: FindBook
        //
        // Purpose: Finds a book given a certain title
        //**************************************************** 
        public Book FindBook(string title)
        {
            //LINQ with a string return value rather than an entire class
            IEnumerable<Book> Bookresult =
                (from b in books
                 where b.Title == title
                 select b);
            foreach (Book x in Bookresult)
            {
                return x;
            }
            return null;
        }


        ////****************************************************
        //// Method: FindAllBook
        //// Purpose: Finds a list of books with the given author's first and last name
        ////**************************************************** 
        //public void FindAllBooks(string first, string last)
        //{
        //    IEnumerable<Book> Bookresult =
        //        (from b in books
        //         where b.authors.First = first
        //         where b.authors.Last = last
        //         select b);



        //}

        //****************************************************
        // Method: ToString
        //
        // Purpose: Prints the publisher info on the screen
        //**************************************************** 
        public override string ToString()
        {
            string s;
            s = "The name of the publisher is: " + Name +
                "The books published are: ";
            foreach (Book x in books)
            {
                s += x.ToString();
            }
            return s;
        }

        #endregion

    }
}
