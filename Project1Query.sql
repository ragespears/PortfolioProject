Select * 
From PortfolioProject..covidDeaths
Where location = 'Canada'
-----------------------------------------------------------------------------------------------------------------------------------------------------
-- Data Broken down by Location
-----------------------------------------------------------------------------------------------------------------------------------------------------


--select Data that we are going to be using
Select location, date, total_cases, new_cases, total_deaths, population
From PortfolioProject..covidDeaths
Order by 1,2


-- Looking at total cases vs total deaths
Select location, date, total_cases, total_deaths, (total_deaths/total_cases) * 100 as death_percentage
From PortfolioProject..covidDeaths
Order by 1,2

-- Looking at total cases vs total deaths in the United States
Select location, date, total_cases, total_deaths, (total_deaths/total_cases) * 100 as death_percentage
From PortfolioProject..covidDeaths
Where location = 'United States'
Order by 1,2

-- looking at total cases vs total population in locations with states in the name
Select location, date, total_cases, population, (total_cases/population) * 100 as case_percentage
From PortfolioProject..covidDeaths
Where location like '%states%'
Order by 1,2

--Looking at total cases vs total population in locations with exactly United States in the name
Select location, date, total_cases, population, (total_cases/population) * 100 as case_percentage
From PortfolioProject..covidDeaths
Where location = 'United States'
Order by 1,2

--Looking at countries with the highest infection rate compared to the population
Select location, population, MAX(total_cases) as highest_infection_count, MAX((total_cases/population)) * 100 as highest_case_percent
From PortfolioProject..covidDeaths
Group by location, population
Order by highest_case_percent desc

--Looking at countries with the highest death count
Select location, MAX(cast(total_deaths as int)) as total_death_count
From PortfolioProject..covidDeaths
Where continent is not null
Group by location
Order by total_death_count desc

-- for some reason the website also has income brackets included in the location so we had to weed those out through the where clause with and operators and not equal to operators
Select location, MAX(cast(total_deaths as int)) as total_death_count
From PortfolioProject..covidDeaths
Where continent is null and location != 'Upper middle income' and location != 'High income' and location != 'Lower middle income' and location != 'Low income'
Group by location
Order by total_death_count desc

-----------------------------------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------
-- Data broken down by continent
-----------------------------------------------------------------------------------------------------------------------------------------------------
-- Looking at total death count of a continent
Select continent, MAX(cast(total_deaths as int)) as total_death_count
From PortfolioProject..covidDeaths
Where continent is not null
Group by continent
Order by total_death_count desc

-----------------------------------------------------------------------------------------------------------------------------------------------------
-- Global Numbers
-----------------------------------------------------------------------------------------------------------------------------------------------------
-- Total cases per day globally vs total deaths globally and percentage of death globally
Select date, SUM(new_cases) as total_cases, SUM(cast(new_deaths as int)) as total_deaths, SUM(cast(new_deaths as int))/SUM(new_cases) * 100 as death_percentage
From PortfolioProject..covidDeaths
Where continent is not null
Group by date
Order by 1,2

-- total cases globally vs total deaths globally vs death percentage all as a whole not daily
Select SUM(new_cases) as total_cases, SUM(cast(new_deaths as int)) as total_deaths, SUM(cast(new_deaths as int))/SUM(new_cases) * 100 as death_percentage
from PortfolioProject..covidDeaths
Where continent is not null
Order by 1,2

-- Joining the covid deaths table with the covid vaccinations table
Select * 
From PortfolioProject..covidDeaths dea
Join PortfolioProject..covidVaccinations vac on dea.location = vac.location
and dea.date = vac.date

-- Use CTE
-- Looking at Total population vs  total Vaccinations daily
With PopvsVac ( Continent, location, date, population, new_vaccinations, vaccinations_rolling_count)
as (
Select dea.continent, dea.location, dea.date, dea.population, vac.new_vaccinations, SUM(CONVERT(float, vac.new_vaccinations)) Over (Partition by dea.location Order by dea.location, dea.date) as vaccinations_rolling_count
From PortfolioProject..covidDeaths dea
Join PortfolioProject..covidVaccinations vac on dea.location = vac.location and dea.date = vac.date
Where dea.continent is not null
)
Select *, (vaccinations_rolling_count/Population) * 100 as percent_vaccinated_daily
From PopvsVac


-- Temp table
Drop Table if exists #PercentPopulationVaccinated
Create Table #PercentPopulationVaccinated
(
continent nvarchar(255),
location nvarchar(255),
date datetime,
population numeric,
new_vaccinations numeric,
vaccinations_rolling_count numeric,
)

Insert into #PercentPopulationVaccinated
Select dea.continent, dea.location, dea.date, dea.population, vac.new_vaccinations, SUM(CONVERT(float, vac.new_vaccinations)) Over (Partition by dea.location Order by dea.location, dea.date) as vaccinations_rolling_count
From PortfolioProject..covidDeaths dea
Join PortfolioProject..covidVaccinations vac on dea.location = vac.location and dea.date = vac.date
Where dea.continent is not null


Select *, (vaccinations_rolling_count/Population) * 100 as percent_vaccinated_daily
From #PercentPopulationVaccinated
Order by 2,3


-- Creating view to store data for later visualiztions
Drop View if exists PercentPopulationVaccinated

Create View PercentPopulationVaccinated as
Select dea.continent, dea.location, dea.date, dea.population, vac.new_vaccinations, SUM(CONVERT(float, vac.new_vaccinations)) Over (Partition by dea.location Order by dea.location, dea.date) as vaccinations_rolling_count
From PortfolioProject..covidDeaths dea
Join PortfolioProject..covidVaccinations vac on dea.location = vac.location and dea.date = vac.date
Where dea.continent is not null

Select * 
From PercentPopulationVaccinated