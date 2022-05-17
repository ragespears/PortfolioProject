Select * 
From PortfolioProject..NashvilleHousing

-----------------------------------------------------------------

--Standardize Date Format

Select SaleDate, CONVERT(Date,SaleDate)
From PortfolioProject..NashvilleHousing

Alter Table NashvilleHousing
Add SaleDateConverted Date;

Update PortfolioProject..NashvilleHousing
Set SaleDateConverted = CONVERT(Date,SaleDate)

Select * 
From PortfolioProject..NashvilleHousing
Where PropertyAddress is null

-----------------------------------------------------------------------------------
--Looking at the parcelID to determine the address of null values inthe dataset
-----------------------------------------------------------------------------------
Select * 
From PortfolioProject..NashvilleHousing
Order by ParcelID

-----------------------------------------------------------------------------------
--Making a column for the null values of a to be replaced by the addresses from b
-----------------------------------------------------------------------------------
Select a.ParcelID, a.PropertyAddress, b.ParcelID, b.PropertyAddress, ISNULL(a.PropertyAddress,b.PropertyAddress)
From PortfolioProject..NashvilleHousing a
JOIN PortfolioProject..NashvilleHousing b
	ON a.parcelID = b.ParcelID
	AND a.[UniqueID ] != b.[UniqueID ]
Where a.PropertyAddress is null

-----------------------------------------------------------------------------------
--Updating the dataset to have matching addresses for the null values of a
-----------------------------------------------------------------------------------
Update a
SET PropertyAddress = ISNULL(a.PropertyAddress,b.PropertyAddress)
From PortfolioProject..NashvilleHousing a
JOIN PortfolioProject..NashvilleHousing b
	ON a.parcelID = b.ParcelID
	AND a.[UniqueID ] != b.[UniqueID ]
Where a.PropertyAddress is null


-----------------------------------------------------------------------------------
--Breaking up the address data into individual columns (Address, City)
-----------------------------------------------------------------------------------

Select PropertyAddress
From PortfolioProject..NashvilleHousing

-----------------------------------------------------------------------------------
--Breaking up the address data into just the address **-1 to not show the comma
-----------------------------------------------------------------------------------
Select
SUBSTRING(PropertyAddress, 1, CHARINDEX(',', PropertyAddress) -1) as Address,
SUBSTRING(PropertyAddress, CHARINDEX(',', PropertyAddress) +1, LEN(PropertyAddress)) as City
From PortfolioProject..NashvilleHousing


-----------------------------------------------------------------------------------
--Need to add columns before assigning them values
-----------------------------------------------------------------------------------
Alter Table NashvilleHousing
Add PropertySplitAddress NVARCHAR(255);

Alter Table NashvilleHousing
Add PropertySplitCity NVARCHAR(255);


-----------------------------------------------------------------------------------
--Update the columns with the information from the split address to their respective columns
-----------------------------------------------------------------------------------
-- SUBSTRING (Expression, Starting Position, Length)
Update PortfolioProject..NashvilleHousing
Set PropertySplitAddress = SUBSTRING(PropertyAddress, 1, CHARINDEX(',', PropertyAddress) -1)

Update PortfolioProject..NashvilleHousing
Set PropertySplitCity = SUBSTRING(PropertyAddress, CHARINDEX(',', PropertyAddress) +1, LEN(PropertyAddress))


-----------------------------------------------------------------------------------
--Next do the same with the OwnerAddress
-----------------------------------------------------------------------------------
Select OwnerAddress
From PortfolioProject..NashvilleHousing

-----------------------------------------------------------------------------------
--PARSENAME seperates string with a delimiter of a period
--Our dataset had the delimiter as a comma so we replaced that and then used the
--PARSENAME function to seperate the address, city, and state
--PARSENAME also does things backwards and take from the end so we made it 3,2,1
--to correctly show the address,city then state
-----------------------------------------------------------------------------------

Select
PARSENAME(REPLACE(OwnerAddress, ',', '.'), 3),
PARSENAME(REPLACE(OwnerAddress, ',', '.'), 2),
PARSENAME(REPLACE(OwnerAddress, ',', '.'), 1)
From PortfolioProject..NashvilleHousing


-----------------------------------------------------------------------------------
--Need to add columns before assigning them values
-----------------------------------------------------------------------------------
Alter Table NashvilleHousing
Add OwnerSplitAddress NVARCHAR(255);

Alter Table NashvilleHousing
Add OwnerSplitCity NVARCHAR(255);

Alter Table NashvilleHousing
Add OwnerSplitState NVARCHAR(255);

-----------------------------------------------------------------------------------------------
--Update the columns with the information from the split address to their respective columns
-----------------------------------------------------------------------------------------------
Update PortfolioProject..NashvilleHousing
Set OwnerSplitAddress = PARSENAME(REPLACE(OwnerAddress, ',', '.'), 3)

Update PortfolioProject..NashvilleHousing
Set OwnerSplitCity = PARSENAME(REPLACE(OwnerAddress, ',', '.'), 2)

Update PortfolioProject..NashvilleHousing
Set OwnerSplitState = PARSENAME(REPLACE(OwnerAddress, ',', '.'), 1)


-----------------------------------------------------------------------------------
--Change Y's and N's to Yes and NO in "Sold as Vacant" since the data has both Y,N,Yes,No
-----------------------------------------------------------------------------------

Select Distinct(SoldAsVacant)
From PortfolioProject..NashvilleHousing

Select SoldAsVacant,
CASE When SoldAsVacant = 'Y' THEN 'Yes'
	 When SoldAsVacant = 'N' THEN 'No'
	 Else SoldAsVacant
	 END
From PortfolioProject..NashvilleHousing

Update NashvilleHousing
SET SoldAsVacant = CASE When SoldAsVacant = 'Y' THEN 'Yes'
	 When SoldAsVacant = 'N' THEN 'No'
	 Else SoldAsVacant
	 END
From PortfolioProject..NashvilleHousing

-----------------------------------------------------------------------------------
--Removing Duplicates using a CTE
-----------------------------------------------------------------------------------
WITH RowNumCTE AS(
Select *,
	ROW_NUMBER() OVER (
	PARTITION BY ParcelID,
				 PropertyAddress,
				 SalePrice,
				 SaleDate,
				 LegalReference
				 Order by uniqueID)
				 row_num
From PortfolioProject..NashvilleHousing
)


-----------------------------------------------------------------------------------
--Deleting the duplicates found from the CTE
-----------------------------------------------------------------------------------
DELETE
From RowNumCTE
Where row_num > 1
Order by PropertyAddress

Select *
From RowNumCTE
Where row_num > 1
Order by PropertyAddress

-----------------------------------------------------------------------------------
--Deleting unused columns from the table
-----------------------------------------------------------------------------------
ALTER TABLE PortfolioProject..NashvilleHousing
DROP COLUMN OwnerAddress, TaxDistrict, PropertyAddress, SaleDate

Select *
From PortfolioProject..NashvilleHousing
















