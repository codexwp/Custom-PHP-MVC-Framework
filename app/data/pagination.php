<?php
/**
 * Created by PhpStorm.
 * User: SAIFUL
 * Date: 10/15/2020
 * Time: 12:41 PM
 */
?>
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center ">
        <li class="page-item <?php if($paginate_info['current_page']==1){echo 'disabled';} ?>">
            <a class="page-link" href="<?=$paginate_info['url']?>?pageno=1<?=$parameter?>" aria-label="最初">最初</a>
        </li>

        <li class="page-item <?php if($paginate_info['current_page']<=1){echo 'disabled';} ?>">
            <a class="page-link" href="<?=$paginate_info['url']?>?pageno=<?=$paginate_info['current_page']-1?><?=$parameter?>" aria-label="前">前</a>
        </li>

        <?php
        $total_page = intval($paginate_info['total_pages']);
        $current_page = intval($paginate_info['current_page']);
        if($total_page>=7){
            if($current_page<=3){
                $start_page = 1;
                $end_page = 7;
            }
            else if($current_page>$total_page){
                $start_page = 1;
                $end_page =0;
            }
            else if($current_page + 3 >$total_page){
                $diff = $total_page - $current_page;
                $start_page = $current_page - (7-$diff);
                $start_page++;
                $end_page = $total_page;
            }
            else {
                $start_page = $current_page - 3;
                $end_page = $current_page + 3;
            }
        }
        else{
            $start_page = 1;
            $end_page = $total_page;
        }
        for($i= $start_page;$i<=$end_page;$i++){
            $pagi_url = $paginate_info['url'].'?pageno='.$i;
            if($i==$paginate_info['current_page'])
                echo '<li class="page-item active"><a class="page-link" href="'.$pagi_url.$parameter.'">'.$i.'</a></li>';
            else
                echo '<li class="page-item"><a class="page-link" href="'.$pagi_url.$parameter.'">'.$i.'</a></li>';
        }
        ?>

        <li class="page-item <?php if($paginate_info['total_pages']==$paginate_info['current_page']){echo 'disabled';} ?>">
            <a class="page-link" href="<?=$paginate_info['url']?>?pageno=<?=$paginate_info['current_page']+1?><?=$parameter?>" aria-label="次">次</a>
        </li>

        <li class="page-item <?php if($paginate_info['total_pages']==$paginate_info['current_page']){echo 'disabled';} ?>">
            <a class="page-link" href="<?=$paginate_info['url']?>?pageno=<?=$paginate_info['total_pages']?><?=$parameter?>" aria-label="最後">最後</a>
        </li>
    </ul>
</nav>