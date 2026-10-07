<?php

namespace Tests\Unit;

use App\Services\VietnameseTokenizerService;
use Tests\TestCase;

class VietnameseTokenizerTest extends TestCase
{
    protected VietnameseTokenizerService $tokenizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenizer = app(VietnameseTokenizerService::class);
    }

    /**
     * Test nạp từ điển Viet74K và tách từ tiếng Việt chuẩn xác
     */
    public function test_tokenize_extracts_vietnamese_compound_words_accurately(): void
    {
        $input = 'phòng trọ khép kín có ban công và máy giặt gần trường đại học';
        $tokens = $this->tokenizer->tokenize($input);
        $compounds = $this->tokenizer->extractCompoundWords($input);

        // Kiểm tra các từ ghép quan trọng của tiếng Việt được nhận diện
        $this->assertContains('khép kín', $compounds);
        $this->assertContains('ban công', $compounds);
        $this->assertContains('máy giặt', $compounds);
        $this->assertContains('đại học', $compounds);

        // Kiểm tra thứ tự và danh sách tokens: 'phòng trọ' là một từ ghép hoàn chỉnh
        $this->assertContains('phòng trọ', $tokens);
        $this->assertContains('máy giặt', $tokens);
    }

    /**
     * Test nhận diện từ ghép bất động sản và tiện nghi
     */
    public function test_tokenize_real_estate_domain_terms(): void
    {
        $input = 'căn hộ chung cư mini có gác lửng nuôi thú cưng khóa vân tay';
        $compounds = $this->tokenizer->extractCompoundWords($input);

        $this->assertContains('căn hộ', $compounds);
        $this->assertContains('gác lửng', $compounds);
        $this->assertContains('thú cưng', $compounds);
        $this->assertContains('khóa vân tay', $compounds);
    }

    /**
     * Test kiểm tra sự tồn tại của từ trong từ điển Viet74K
     */
    public function test_has_word_in_viet74k_dictionary(): void
    {
        $this->assertTrue($this->tokenizer->hasWord('máy giặt'));
        $this->assertTrue($this->tokenizer->hasWord('ban công'));
        $this->assertTrue($this->tokenizer->hasWord('cầu giấy'));
        $this->assertTrue($this->tokenizer->hasWord('thanh xuân'));
        $this->assertFalse($this->tokenizer->hasWord('xyzabc_random_nonexistent'));
    }
}
