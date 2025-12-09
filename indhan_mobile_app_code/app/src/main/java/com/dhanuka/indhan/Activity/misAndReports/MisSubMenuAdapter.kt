package com.dhanuka.indhan.Activity.misAndReports


import android.app.Activity
import android.content.Context
import android.text.TextUtils
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.RelativeLayout
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.dhanuka.indhan.Activity.misAndReports.model.MisSubMenu
import com.dhanuka.indhan.Constants.Constants.LIVEURL
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.layout_menu_item.view.*

class MisSubMenuAdapter(
    val context: Context,
    val subMenuList: List<MisSubMenu>,
    private val mActivityContext: Activity
) : RecyclerView.Adapter<MisSubMenuAdapter.ViewHolder>() {

    private lateinit var clickListener: SubMenuClickListener

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view ->
                if ((subMenuList != null) && (subMenuList.size > adapterPosition) && (adapterPosition != -1)) {
                    clickListener.onClick(view, subMenuList[adapterPosition])
                }
            }
        }
    }

    fun setClickListener(itemClickListener: SubMenuClickListener) {
        this.clickListener = itemClickListener
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): MisSubMenuAdapter.ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_menu_item, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(holder: MisSubMenuAdapter.ViewHolder, position: Int) {
        val catName = holder.itemView.tvMeCategoryName
        if (!TextUtils.isEmpty(subMenuList[position].image_url)) {
            Glide.with(mActivityContext)
                .load(LIVEURL + subMenuList[position].image_url)
                .into(holder.itemView.IvImage)
        } else {
            val layoutParams = catName.layoutParams as RelativeLayout.LayoutParams
            layoutParams.addRule(RelativeLayout.CENTER_IN_PARENT, RelativeLayout.TRUE)
            catName.layoutParams = layoutParams
            catName.textSize = 25f
        }
        catName.text = subMenuList[position].name
    }

    override fun getItemCount(): Int {
        return subMenuList.size
    }

    interface SubMenuClickListener {
        fun onClick(view: View, subMenu: MisSubMenu)
    }


}