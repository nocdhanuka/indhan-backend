package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.DesignationDataList

@Dao
interface DesignationDataListDao {
    @Insert
    fun insert(data: DesignationDataList?): Long

    @get:Query("SELECT * FROM DesignationDataList")
    val allDesignations: List<DesignationDataList?>?

    @Query("DELETE FROM DesignationDataList")
    fun deleteTableData()
}