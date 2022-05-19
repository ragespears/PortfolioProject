using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Runtime.Serialization;
using System.Runtime.Serialization.Json;
using System.Runtime.CompilerServices;
using System.Security.Policy;
using Microsoft.Office.Interop.Excel;
using HW1DLL;
//using authorAndBook;
//using classBook;
//using classPublisher;

namespace HW1
{
    class Program
    {
        //****************************************************
        // Method: Main
        //
        // Purpose: Set up the menu to use all the functions
        //**************************************************** 
        static void Main(string[] args)
        {
            Program p = new Program();
            List<Author> a = new List<Author>();
            List<Book> bs = new List<Book>();
            Author myauthor = new Author();
            Book b = new Book();
            String answer;
            Console.Clear();
            publisher pub = new publisher();
            

            do
            {
                System.Console.WriteLine("1 - Read publisher from JSON file");
                System.Console.WriteLine("2 - Read publisher from XML file");
                System.Console.WriteLine("3 - Write publisher to JSON file");
                System.Console.WriteLine("4 - Write publisher to XML file");
                System.Console.WriteLine("5 - Write publisher to Excel file");
                System.Console.WriteLine("6 - Display all publisher data on screen");
                System.Console.WriteLine("7 - Find Book");
                System.Console.WriteLine("8 - Exit");
                System.Console.Write("Enter Choice: ");
                answer = Console.ReadLine();
                if (answer == "1")
                {
                    pub = p.deserialization_PJson(pub);
                }
                if (answer == "2")
                {
                    pub = p.deserialization_PXML(pub);
                }
                if (answer == "3")
                {
                    p.serialization_PJson(pub);
                }
                if (answer == "4")
                {
                    p.serialization_PXML(pub);
                }
                //if (answer == "5")
                //{
                //    p.deserialization_AJson();
                //}
                if (answer == "6")
                {
                    p.publisherPrint(pub);
                }
                if (answer == "7")
                {
                    p.BookFind(pub);
                }
                if (answer == "8")
                {
                    System.Environment.Exit(0);
                }

            }
            while (answer != "8");
        }
        #region AuthorJSON
        //****************************************************
        // Method: serialization_AJson
        //
        // Purpose: write author in a json file
        //**************************************************** 
        public void serialization_AJson(Author a)
        {

            System.Console.Write("Please input the file name: ");
            string filename = Console.ReadLine();
            FileStream writer = new FileStream(filename, FileMode.Create, FileAccess.Write);
            DataContractJsonSerializer ser;
            ser = new DataContractJsonSerializer(typeof(Author));
            ser.WriteObject(writer, a);
            writer.Dispose();
            Console.WriteLine("Press enter to continue back to the menu");
            Console.ReadKey();
            return;
        }
        //****************************************************
        // Method: deserialization_AJson
        //
        // Purpose: read author in a json file
        //**************************************************** 
        //Deserialization of class Book using JSON
        public Author deserialization_AJson(Author a1)
        {
            System.Console.Write("Please input the file name: ");
            string filename1 = Console.ReadLine();
            FileStream reader = new FileStream(filename1, FileMode.Open, FileAccess.Read);
            DataContractJsonSerializer inputSerializer;
            inputSerializer = new DataContractJsonSerializer(typeof(Author));
            a1 = (Author)inputSerializer.ReadObject(reader);
            reader.Dispose();
            Console.WriteLine("Press enter to continue back to the menu");
            Console.ReadKey();
            return a1;
        }

        #endregion

        #region BookJSON
        //****************************************************
        // Method: serialization_BJson
        //
        // Purpose: write book in a json file
        //**************************************************** 
        public void serialization_BJson(Book b)
        {
            //Serialization of class Book into a json file called Book.json if the user calls it that
            System.Console.Write("Please input the file name: ");
            string filename2 = Console.ReadLine();
            FileStream writer1 = new FileStream(filename2, FileMode.Create, FileAccess.Write);
            DataContractJsonSerializer ser1;
            ser1 = new DataContractJsonSerializer(typeof(Book));
            ser1.WriteObject(writer1, b);
            writer1.Dispose();
            Console.WriteLine("Press enter to continue back to the menu");
            Console.ReadKey();
            return;
        }

        //****************************************************
        // Method: deserialization_BJson
        //
        // Purpose: read book in a json file
        //**************************************************** 
        public Book deserialization_BJson(Book b1)
        {
            //Deserialization of class Book using JSON
            System.Console.Write("Please input the file name: ");
            string filename3 = Console.ReadLine();
            FileStream reader1 = new FileStream(filename3, FileMode.Open, FileAccess.Read);
            DataContractJsonSerializer inputSerializer1;
            inputSerializer1 = new DataContractJsonSerializer(typeof(Book));
            b1 = (Book)inputSerializer1.ReadObject(reader1);
            reader1.Dispose();
            Console.WriteLine("Press enter to continue back to the menu");
            Console.ReadKey();
            return b1;
        }
        #endregion

        #region AuthorXML
        //****************************************************
        // Method: serialization_AXML
        //
        // Purpose: write author in a XML file
        //**************************************************** 
        public void serialization_AXML(Author a2)
        {
            //Serialization of class Author into a XML file called author.XML if the user calls it that
            System.Console.Write("Please input the file name: ");
            string filename4 = Console.ReadLine();
            FileStream writer2 = new FileStream(filename4, FileMode.Create, FileAccess.Write);
            DataContractSerializer ser2;
            ser2 = new DataContractSerializer(typeof(Author));
            ser2.WriteObject(writer2, a2);
            writer2.Dispose();
            Console.WriteLine("Press enter to continue back to the menu");
            Console.ReadKey();
            return;
        }

        //****************************************************
        // Method: deserialization_AXML
        //
        // Purpose: read author in a XML file
        //**************************************************** 
        public Author deserialization_AXML(Author a3)
        {
            //Deserialization of class Author using XML
            System.Console.Write("Please input the file name: ");
            String filename5 = Console.ReadLine();
            FileStream reader2 = new FileStream(filename5, FileMode.Open, FileAccess.Read);
            DataContractSerializer inputSerializer2;
            inputSerializer2 = new DataContractSerializer(typeof(Author));
            a3 = (Author)inputSerializer2.ReadObject(reader2);
            reader2.Dispose();
            Console.WriteLine("Press enter to continue back to the menu");
            Console.ReadKey();
            return a3;
        }
        #endregion

        #region BookXML
        //****************************************************
        // Method: serialization_BXML
        //
        // Purpose: write book in a XML file
        //**************************************************** 
        public void serialization_BXML(Book b2)
        {
            System.Console.Write("Please input the file name: ");
            string filename6 = Console.ReadLine();
            FileStream writer3 = new FileStream(filename6, FileMode.Create, FileAccess.Write);
            DataContractSerializer ser3;
            ser3 = new DataContractSerializer(typeof(Book));
            ser3.WriteObject(writer3, b2);
            writer3.Dispose();
            Console.WriteLine("Press enter to continue back to the menu");
            Console.ReadKey();
            return;
        }

        //****************************************************
        // Method: deserialization_BXML
        //
        // Purpose: write book in a XML file
        //**************************************************** 
        public Book deserialization_BXML(Book b3)
        {

            System.Console.Write("Please input the file name: ");
            String filename7 = Console.ReadLine();
            FileStream reader3 = new FileStream(filename7, FileMode.Open, FileAccess.Read);
            DataContractSerializer inputSerializer3;
            inputSerializer3 = new DataContractSerializer(typeof(Book));
            b3 = (Book)inputSerializer3.ReadObject(reader3);
            reader3.Dispose();
            Console.WriteLine("Press enter to continue back to the menu");
            Console.ReadKey();
            return b3;
        }
        #endregion
        #region print functions
        #region authorPrint
        //****************************************************
        // Method: authorPrint
        //
        // Purpose: print author on the screen
        //**************************************************** 
        public void authorPrint(Author a6)
        {
            System.Console.WriteLine(a6.ToString());
            Console.WriteLine("Press enter to continue back to the menu");
            Console.ReadKey();
            Console.Clear();
            return;
        }
        #endregion

        #region bookPrint
        //****************************************************
        // Method: bookPrint
        //
        // Purpose: print book on the screen
        //**************************************************** 
        public void bookPrint(Book b6)
        {
            Console.WriteLine(b6.ToString());
            Console.WriteLine("Press enter to continue back to the menu");
            Console.ReadKey();
            Console.Clear();
            return;
        }
        #endregion

        #region PublisherPrint
        //****************************************************
        // Method: publisherPrint
        //
        // Purpose: print Publisher on the screen
        //**************************************************** 
        public void publisherPrint(publisher p4)
        {
            System.Console.WriteLine(p4.ToString());
            Console.WriteLine("Press enter to continue back to the menu");
            Console.ReadKey();
            Console.Clear();
            return;
        }
        #endregion
        #endregion

        #region PublisherJSON
        //****************************************************
        // Method: serialization_PJson
        //
        // Purpose: write publisher in a json file
        //**************************************************** 
        public void serialization_PJson(publisher p)
        {

            System.Console.Write("Please input the file name: ");
            string filename8 = Console.ReadLine();
            FileStream writer4 = new FileStream(filename8, FileMode.Create, FileAccess.Write);
            DataContractJsonSerializer ser4;
            ser4 = new DataContractJsonSerializer(typeof(publisher));
            ser4.WriteObject(writer4, p);
            writer4.Dispose();
            Console.WriteLine("Press enter to continue back to the menu");
            Console.ReadKey();
            return;
        }
        //****************************************************
        // Method: deserialization_PJson
        //
        // Purpose: read publisher from a json file
        //**************************************************** 
        public publisher deserialization_PJson(publisher p1)
        {

            System.Console.Write("Please input the file name: ");
            string filename9 = Console.ReadLine();
            FileStream reader4 = new FileStream(filename9, FileMode.Open, FileAccess.Read);
            DataContractJsonSerializer inputSerializer4;
            inputSerializer4 = new DataContractJsonSerializer(typeof(publisher));
            p1 = (publisher)inputSerializer4.ReadObject(reader4);
            reader4.Dispose();
            Console.WriteLine("Press enter to continue back to the menu");
            Console.ReadKey();
            return p1;
        }
        #endregion

        #region PublisherXML
        //****************************************************
        // Method: serialization_PJson
        //
        // Purpose: write publisher in a json file
        //**************************************************** 
        public void serialization_PXML(publisher p2)
        {

            System.Console.Write("Please input the file name: ");
            string filename10 = Console.ReadLine();
            FileStream writer5 = new FileStream(filename10, FileMode.Create, FileAccess.Write);
            DataContractSerializer ser;
            ser = new DataContractSerializer(typeof(publisher));
            ser.WriteObject(writer5, p2);
            writer5.Dispose();
            Console.WriteLine("Press enter to continue back to the menu");
            Console.ReadKey();
            Main(null);
        }
        //****************************************************
        // Method: deserialization_PJson
        //
        // Purpose: read publisher from a json file
        //**************************************************** 
        //Deserialization of class Book using JSON
        public publisher deserialization_PXML(publisher p3)
        {

            System.Console.Write("Please input the file name: ");
            string filename11 = Console.ReadLine();
            FileStream reader5 = new FileStream(filename11, FileMode.Open, FileAccess.Read);
            DataContractSerializer inputSerializer;
            inputSerializer = new DataContractSerializer(typeof(publisher));
            p3 = (publisher)inputSerializer.ReadObject(reader5);
            reader5.Dispose();
            Console.WriteLine("Press enter to continue back to the menu");
            Console.ReadKey();
            return p3;
        }
        #endregion

        public void BookFind(publisher p5)
        {
            Console.WriteLine("Please enter a title to find a book with that title");
            string t = Console.ReadLine();
            Console.WriteLine(p5.FindBook(t));
            Console.WriteLine("Press enter to continue back to the menu");
            Console.ReadKey();
        }
    }
}