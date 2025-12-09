package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model.ActivityDurationModel

@Dao
interface ActivityDurationDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: ActivityDurationModel)

    @Query("SELECT * FROM DurationTable WHERE status =:status AND ticket_no =:ticketNumber")
    fun getAllActivityDurationData(status: Int, ticketNumber: String): List<ActivityDurationModel?>?

    @Query("SELECT * FROM DurationTable WHERE status =:status LIMIT 1")
    fun getSingleActivityDurationData(status: Int): ActivityDurationModel?

    @Query("UPDATE DurationTable SET status =:status WHERE ticket_no =:ticketNumber")
    fun updateActivityDurationUploadedData(status: Int, ticketNumber: String)

    @Query("DELETE FROM DurationTable WHERE id =:id")
    fun deleteTableData(id: Int)

    @Query("DELETE FROM DurationTable WHERE ticket_no =:ticket")
    fun deleteTableData(ticket: String)
}