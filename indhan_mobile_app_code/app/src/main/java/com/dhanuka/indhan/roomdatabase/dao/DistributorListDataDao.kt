package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.DistributorListData

@Dao
interface DistributorListDataDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: DistributorListData?): Long

    @Query("SELECT * FROM DistributorListData")
    fun allDistributors(): List<DistributorListData?>?

    @Query("SELECT * FROM DistributorListData WHERE is_distributor =:type")
    fun allDistributorsByType(
        type: String
    ): List<DistributorListData?>?

    @Query("DELETE FROM DistributorListData")
    fun deleteTableData()
}