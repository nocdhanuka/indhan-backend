package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.teaEstate.AddTeaEstateStgBranchModel

@Dao
interface AddTeaEstateStgBranchDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: AddTeaEstateStgBranchModel?): Long

    @Query("SELECT * FROM AddTeaEstateStgBranchModel WHERE status =:status LIMIT 1")
    fun getSingleAddTeaEstateStgBranchData(
        status: Int
    ): AddTeaEstateStgBranchModel?

    @Query("SELECT * FROM AddTeaEstateStgBranchModel")
    fun getAllAddTeaEstateStgBranchData(): List<AddTeaEstateStgBranchModel>?

    @Query("SELECT * FROM AddTeaEstateStgBranchModel WHERE status =:status AND hitTime<=:time LIMIT 1")
    fun getSingleAddTeaEstateStgBranchData(status: Int, time: Long): AddTeaEstateStgBranchModel?

    @Query("SELECT * FROM AddTeaEstateStgBranchModel WHERE hitTime<=:time LIMIT 1")
    fun getSosAddTeaEstateStgBranchData(time: Long): AddTeaEstateStgBranchModel?

    @Query("UPDATE AddTeaEstateStgBranchModel SET status =:status ,hitTime =:lastHitTime WHERE teaEstateId=:id")
    fun updateAddTeaEstateStgBranchData(id: Long, lastHitTime: Long, status: Int)

    @Query("DELETE FROM AddTeaEstateStgBranchModel WHERE teaEstateId =:id")
    fun deleteAddTeaEstateStgBranchData(id: Long)

}