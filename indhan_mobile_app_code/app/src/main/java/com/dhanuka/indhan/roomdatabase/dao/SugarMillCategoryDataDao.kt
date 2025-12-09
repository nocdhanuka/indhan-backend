package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillCategoryData

@Dao
interface SugarMillCategoryDataDao {
    @Insert
    fun insert(data: SugarMillCategoryData?): Long

    @get:Query("SELECT * FROM SugarMillCategoryData")
    val allCategoryData: List<SugarMillCategoryData?>?

    @Query("DELETE FROM SugarMillCategoryData")
    fun deleteTableData()
}