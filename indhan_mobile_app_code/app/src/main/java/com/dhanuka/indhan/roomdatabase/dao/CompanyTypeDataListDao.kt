package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.CompanyTypeDataList

@Dao
interface CompanyTypeDataListDao {
    @Insert
    fun insert(data: CompanyTypeDataList?): Long

    @get:Query("SELECT * FROM CompanyTypeDataList")
    val allCompanyType: List<CompanyTypeDataList?>?

    @Query("DELETE FROM CompanyTypeDataList")
    fun deleteTableData()
}