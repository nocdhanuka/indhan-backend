package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillData

@Dao
interface SugarMillListDataDao {
    @Insert
    fun insert(data: SugarMillData?): Long

    @get:Query("SELECT * FROM SugarMillData")
    val allSugarMill: List<SugarMillData?>?

    @Query("DELETE FROM SugarMillData")
    fun deleteTableData()
}