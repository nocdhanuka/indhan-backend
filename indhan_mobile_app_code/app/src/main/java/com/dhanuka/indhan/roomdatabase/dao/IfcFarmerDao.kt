package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model.AddIfcFarmer

@Dao
interface IfcFarmerDao {

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: AddIfcFarmer)

    @Query("SELECT * FROM AddIfcFarmer WHERE status =:status AND ticket_no =:ticketNumber")
    fun getAllIfcFarmerData(status: Int, ticketNumber: String): List<AddIfcFarmer?>

    @Query("SELECT * FROM AddIfcFarmer")
    fun getAllIfcFarmerDataList(): List<AddIfcFarmer?>

    @Query("SELECT * FROM AddIfcFarmer WHERE ticket_no =:ticketNumber")
    fun getAllIfcFarmerDataForTicket(ticketNumber: String): List<AddIfcFarmer?>

    @Query("SELECT * FROM AddIfcFarmer WHERE ticket_no =:ticketNumber AND uploaded =:isuploaded")
    fun getAllIfcFarmerDataForTicket(ticketNumber: String, isuploaded: Boolean): List<AddIfcFarmer?>

    @Query("SELECT * FROM AddIfcFarmer WHERE uploaded =:isuploaded")
    fun getAllIfcFarmerDataForTicketNotUploaded(isuploaded: Boolean): List<AddIfcFarmer?>

    @Query("SELECT * FROM AddIfcFarmer WHERE status =:status AND ticket_no =:ticketNumber LIMIT 1")
    fun getSingleIfcFarmerData(status: Int, ticketNumber: String): AddIfcFarmer?

    @Query("SELECT * FROM AddIfcFarmer WHERE status =:status AND uploaded =:isuploaded LIMIT 1")
    fun getSingleIfcFarmerData(status: Int, isuploaded: Boolean): AddIfcFarmer?

    @Query("UPDATE AddIfcFarmer SET status =:status WHERE ticket_no =:ticketNumber AND mobile =:phoneNumber")
    fun updateIfcFarmerUploadedData(status: Int, ticketNumber: String, phoneNumber: String)

    @Query("UPDATE AddIfcFarmer SET activityDistance =:distance WHERE ticket_no =:ticketNumber AND mobile =:phoneNumber")
    fun updateIfcFarmerUploadedData(distance: Double, ticketNumber: String, phoneNumber: String)

    @Query("DELETE FROM AddIfcFarmer WHERE serverId =:id")
    fun deleteTableData(id: Int)

    @Query("UPDATE AddIfcFarmer SET status =:i WHERE ticket_no =:s")
    fun updateIfcFarmerUploadedData(i: Int, s: String)

}