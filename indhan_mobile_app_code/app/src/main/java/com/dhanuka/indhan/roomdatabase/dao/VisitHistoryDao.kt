package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.visithistory.VisitHistoryFarmerBean

@Dao
interface VisitHistoryDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: VisitHistoryFarmerBean.Data?): Long

    @Query("SELECT * FROM VisitHistoryFarmer")
    fun allVisitHistoryList(): List<VisitHistoryFarmerBean.Data>?

    @Query("DELETE FROM VisitHistoryFarmer")
    fun deleteTableData()
}