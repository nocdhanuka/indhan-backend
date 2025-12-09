package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.FlDataList

@Dao
interface FlDataListDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: FlDataList?): Long

    @get:Query("SELECT * FROM FlDataList")
    val allFlListType: List<FlDataList?>?

    @Query("SELECT * FROM FlDataList WHERE manager_id =:managerId")
    fun getAllFlByManagerEcn(managerId: String?): List<FlDataList?>?

    @Query("DELETE FROM FlDataList")
    fun deleteTableData()
}